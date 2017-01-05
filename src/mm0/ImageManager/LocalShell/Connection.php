<?php

namespace mm0\ImageManager\LocalShell;

use mm0\ImageManager\AbstractConnection;
use mm0\ImageManager\ConnectionResponse;
use mm0\ImageManager\ConnectionInterface;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use mm0\ImageManager\Log;

/**
 * Class Connection
 * @package mm0\ImageManager
 */
class Connection extends AbstractConnection
{

    /**
     * Connection constructor.
     */
    function __construct()
    {
    }

    public function verify()
    {
    }

    /**
     * @return ConnectionResponse
     */
    public function executeCommand($command, $no_sudo = false)
    {
        $command = ($this->isSudoAll() && !$no_sudo ? "sudo " : "") . $command;

        // Hacky way to get stderr, but proc_open seems to block indefinitely
        $tmpfname = tempnam("/tmp", "image_manager");
        $stdout = rtrim(shell_exec($command . " 2>$tmpfname"));
        $stderr = file_get_contents($tmpfname);
        unlink($tmpfname);

        return new ConnectionResponse(
            $command,
            $stdout,
            $stderr
        );
    }

    /**
     * @param string $file
     * @return mixed
     */
    public function getFileContents($file)
    {
        if ($this->file_exists($file)) {
            $contents = file_get_contents($file);
        } else {
            $contents = "";
        }
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
     * @return
     */
    public function writeFileContents($file, $contents, $mode = 0644)
    {
        return boolval(file_put_contents($file, $contents));
    }

    /**
     * @param string $file
     * @return boolean
     */
    public function file_exists($file)
    {
        return file_exists($file);
    }

    /**
     * @param string $directory
     * @return mixed
     */
    public function scandir($directory)
    {
        return scandir($directory);
    }


    /**
     *
     * @param string $directory
     * @return mixed
     */
    public function mkdir($directory)
    {
        return mkdir($directory);
    }

    /**
     * @param string $file
     * @return mixed
     */
    public function rmfile($file)
    {
        Log::logNotice("Deleting file: " . $file);
        if ((is_file($file) === true) || (is_link($file) !== true)) {
            return unlink($file);
        }
        return false;
    }
    /**
     * @param string $directory
     * @return mixed
     */
    public function rmdir($directory)
    {
        Log::logWarning("Warning, *** method rmdir utilized *** on directory: " . $directory );
        if (is_dir($directory) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file) {
                if (in_array($file->getBasename(), array('.', '..')) !== true) {
                    if ($file->isDir() === true) {
                        rmdir($file->getPathName());
                    } else if (($file->isFile() === true) || ($file->isLink() === true)) {
                        unlink($file->getPathname());
                    }
                }
            }

            return rmdir($directory);
        } else if ((is_file($directory) === true) || (is_link($directory) === true)) {
            return unlink($directory);
        }

        return false;
    }



}