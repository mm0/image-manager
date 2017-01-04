<?php

namespace mm0\ImageManager\Configuration;

use mm0\ImageManager\Exceptions\FileNotFoundException;
use mm0\ImageManager\Exceptions\FileNotReadableException;

/**
 * Class Connection
 * @package mm0\ImageManager
 * Requires: PECL libssh2
 */
class SSH
{
    /**
     * @var string
     */
    protected $host;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var string
     */
    protected $user;
    /**
     * @var string
     */
    protected $passphrase;
    /**
     * @var string
     */
    protected $public_key_file;
    /**
     * @var string
     */
    protected $private_key_file;
    /**
     * @var array
     */
    protected $ssh_options;

    /**
     * Configuration constructor.
     * @param $host
     * @param null $port
     * @param $user
     * @param $public_key_file
     * @param $private_key_file
     * @param null $passphrase
     * @param array $ssh_options
     */
    public function __construct(
        $host,
        $port = null,
        $user,
        $public_key_file,
        $private_key_file,
        $passphrase = null,
        array $ssh_options = array()
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->ssh_options = $ssh_options;
        $this->public_key_file = $public_key_file;
        $this->private_key_file = $private_key_file;
        $this->passphrase = $passphrase;
        $this->verify();
    }

    /**
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    protected function verify()
    {
        $this->verifyFiles();

    }

    /**
     * @return string  The host.
     */
    public function host()
    {
        return $this->host;
    }
    /**
     * @return int  The host.
     */
    public function port()
    {
        return $this->port;
    }
    /**
     * @return string  The username.
     */
    public function user()
    {
        return $this->user;
    }
    /**
     * @return string  The Public Key File location.
     */
    public function publicKey()
    {
        return $this->public_key_file;
    }
    /**
     * @return string  The Private Key File location.
     */
    public function privateKey()
    {
        return $this->private_key_file;
    }
    /**
     * @return string  The SSH Key Passphrase.
     */
    public function passphrase()
    {
        return $this->passphrase;
    }
    /**
     * @return array  The ssh options.
     */
    public function options()
    {
        return $this->ssh_options;
    }

    /**
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    protected function verifyFiles()
    {
        foreach (['public_key_file', 'private_key_file'] as $file) {
            if (!file_exists($this->{$file})) {
                throw new FileNotFoundException(
                    "SSH Configuration File: " . $this->{$file} .
                    "Does not exist",
                    0
                );
            } else {
                if (!is_readable($this->{$file})) {
                    throw new FileNotReadableException(
                        "SSH Configuration File: " . $this->{$file} .
                        "Does not have the correct permissions",
                        0
                    );
                }
            }
        }
    }

}