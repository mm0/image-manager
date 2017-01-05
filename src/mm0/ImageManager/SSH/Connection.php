<?php

namespace mm0\ImageManager\SSH;

use mm0\ImageManager\AbstractConnection;
use mm0\ImageManager\Exceptions\SSH2AuthenticationException;
use mm0\ImageManager\Exceptions\ServerNotListeningException;
use mm0\ImageManager\Exceptions\SSH2ConnectionException;
use mm0\ImageManager\ConnectionResponse;
use mm0\ImageManager\Configuration\SSH;
use mm0\ImageManager\Log;

/**
 * Class Connection
 * @package mm0\ImageManager
 */
class Connection extends AbstractConnection
{

    /**
     * @var Resource
     */
    protected $connection;
    /**
     * @var SSH
     */
    protected $config;
    /**
     * @var bool
     */
    protected $authenticated = false;


    protected $sftp_link = null;


    function __construct(SSH $config)
    {
        $this->config = $config;
        $this->verify();
        Log::addWordFilter($this->config->passphrase());
    }

    /**
     * @throws ServerNotListeningException
     */
    public function verify()
    {
        $this->verifySSHServerListening();
        $this->verifyConnection();
    }

    /**
     * @return resource
     */
    public function getConnectionResource($force_reconnect = false)
    {
        if ($this->authenticated && !$force_reconnect) {
            return $this->connection;
        }
        $this->connection = @ssh2_connect(
            $this->config->host(),
            $this->config->port(),
            $this->config->options()
        );
        if (!$this->connection) {
            throw new SSH2ConnectionException(
                "Connection to SSH Server failed unreachable at host: " . $this->config->port() .
                ":" . $this->config->port(),
                0
            );
        }

        $this->sftp_link= ssh2_sftp($this->connection);

        return $this->connection;
    }

    /**
     * @return ConnectionResponse
     */
    public function executeCommand($command, $no_sudo = false)
    {
        $command = ($this->isSudoAll() && !$no_sudo ? "sudo " : "") . $command;
        $stream = ssh2_exec(
            $this->getConnectionResource(),
            $command,
            true
        );
        $stderrStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($stream, true);
        stream_set_blocking($stderrStream, true);
        $stdout = rtrim(stream_get_contents($stream));
        $stderr = rtrim(stream_get_contents($stderrStream));

        return new ConnectionResponse(
            $command,
            $stdout,
            $stderr
        );
    }

    /**
     * @param string $file
     * @return mixed
     * @throws SSH2ConnectionException
     */
    public function getFileContents($file)
    {
        $temp_file = tempnam($this->getTemporaryDirectoryPath(), "");
        Log::logInfo("Temp filename generated: " . $temp_file);
        if (@ssh2_scp_recv($this->getConnectionResource(), $file, $temp_file)) {
            $contents = file_get_contents($temp_file);
        } else {
            $contents = "";
        }
        unlink($temp_file);

        return $contents;
    }

    /**
     * @return string
     */
    public function getTemporaryDirectoryPath()
    {
        return "/tmp/";
    }

    /**
     * @param string $file
     * @param string $contents
     * @param int $mode
     * @throws SSH2ConnectionException
     */
    public function writeFileContents($file, $contents, $mode = 0644)
    {
        Log::logInfo("Writing file: " . $file);
        $temp_file = tempnam($this->getTemporaryDirectoryPath(), "");
        file_put_contents($temp_file, $contents);
        $result = ssh2_scp_send($this->getConnectionResource(), $temp_file, $file, $mode);
        unlink($temp_file);
        return boolval($result);
    }

    /**
     * @throws SSH2AuthenticationException
     * @throws SSH2ConnectionException
     */
    protected
    function verifyCredentials()
    {

        $resource = @ssh2_auth_pubkey_file(
            $this->getConnectionResource(),
            $this->config->user(),
            $this->config->publicKey(),
            $this->config->privateKey(),
            $this->config->passphrase()
        );
        if (!$resource) {
            throw new SSH2AuthenticationException(
                "Authentication  to SSH Server failed. Check credentials: ",
                0
            );
        } else {
            $this->authenticated = true;
        }
    }

    /**
     * @return bool
     * @throws ServerNotListeningException
     */
    protected
    function verifySSHServerListening()
    {
        $serverConn = @stream_socket_client(
            "tcp://" . $this->config->host() . ":" . $this->config->port(),
            $errno,
            $errstr);

        if ($errstr != '') {
            throw new ServerNotListeningException(
                "SSH Server is unreachable at host: " . $this->config->port() .
                ":" . $this->config->port(),
                0
            );
        }
        fclose($serverConn);

        return true;
    }

    /**
     * @throws SSH2AuthenticationException
     */
    protected function verifyConnection()
    {
        if ($this->authenticated) {
            return;
        } else {
            return $this->verifyCredentials();
        }
    }

    /**
     * @param string $file
     * @return boolean
     */
    public function file_exists($file)
    {
        // Note: This might cause segfault if file doesn't exist due to ssh2 lib bug
        clearstatcache();
        return file_exists($this->sftp_path($file));
    }

    /**
     * @param string $directory
     * @return mixed
     */
    public function scandir($directory)
    {
        clearstatcache();
        return @scandir($this->sftp_path($directory));
    }

    /**
     * @param string $directory
     * @return mixed
     */
    public function mkdir($directory)
    {
        clearstatcache();
        return ssh2_sftp_mkdir($this->sftp_link, $directory);
    }
    /**
     * @param string $directory
     * @return mixed
     */
    public function rmfile($file)
    {
        clearstatcache();
        return $this->delete($file,false,'f');
    }
    /**
     * @param string $directory
     * @return mixed
     */
    public function rmdir($directory)
    {
        $this->delete($directory, true);
        clearstatcache();
    }

    public function delete($file, $recursive, $type = false){
        if ( 'f' == $type || $this->is_file($file) )
            return ssh2_sftp_unlink($this->sftp_link, $file);
        if ( ! $recursive )
            return ssh2_sftp_rmdir($this->sftp_link, $file);
        $filelist = $this->dirlist($file);
        if ( is_array($filelist) ) {
            foreach ( $filelist as $filename => $fileinfo) {
                $this->delete($file . '/' . $filename, $recursive, $fileinfo['type']);
            }
        }
        return ssh2_sftp_rmdir($this->sftp_link, $file);
    }

    public function dirlist($path, $include_hidden = true, $recursive = false)
    {
        if ($this->is_file($path)) {
            $limit_file = basename($path);
            $path = dirname($path);
        } else {
            $limit_file = false;
        }

        if (!$this->is_dir($path))
            return false;

        $ret = array();
        $dir = @dir($this->sftp_path($path));

        if (!$dir)
            return false;

        while (false !== ($entry = $dir->read())) {
            $struc = array();
            $struc['name'] = $entry;

            if ('.' == $struc['name'] || '..' == $struc['name'])
                continue; //Do not care about these folders.

            if (!$include_hidden && '.' == $struc['name'][0])
                continue;

            if ($limit_file && $struc['name'] != $limit_file)
                continue;

            $struc['type'] = $this->is_dir($path . '/' . $entry) ? 'd' : 'f';

            if ('d' == $struc['type']) {
                if ($recursive)
                    $struc['files'] = $this->dirlist($path . '/' . $struc['name'], $include_hidden, $recursive);
                else
                    $struc['files'] = array();
            }

            $ret[$struc['name']] = $struc;
        }
        $dir->close();
        unset($dir);
        return $ret;
    }
    public function sftp_path( $path ) {
        if ( '/' === $path ) {
            $path = '/./';
        }
        if($this->sftp_link == null)
            $this->sftp_link= ssh2_sftp($this->getConnectionResource());

        return 'ssh2.sftp://' . $this->sftp_link . '/' . ltrim( $path, '/' );
    }
    public function is_file($file) {
        clearstatcache();
        return is_file( $this->sftp_path( $file ) );
    }
    public function is_dir($path) {
        clearstatcache();
        return is_dir( $this->sftp_path( $path ) );
    }

}