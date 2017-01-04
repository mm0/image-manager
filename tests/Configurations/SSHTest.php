<?php

namespace Configurations;
/**
 * Class SSHTest
 */
class SSHTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \mm0\ImageManager\Configuration\SSH
     */
    private $ssh_configuration;
    private $ip_address = "127.0.0.1";
    private $port = 22;
    private $user = "vagrant";
    private $public_key = "/home/vagrant/.ssh/id_rsa.pub";
    private $private_key = "/home/vagrant/.ssh/id_rsa";
    private $hostkey = "ssh-rsa";
    private $passphrase = "";

    public function setUp()
    {
        $this->setupSSHConfiguration();
    }

    /**
     *
     */
    public function tearDown()
    {
        $this->ssh_configuration = null;
    }


    private function setupSSHConfiguration()
    {
        $this->ssh_configuration = new \mm0\ImageManager\Configuration\SSH(
            $this->ip_address,
            $this->port,
            $this->user,
            $this->public_key,
            $this->private_key,
            $this->passphrase,             // ssh key passphrase
            array('hostkey' => $this->hostkey)
        );
    }
    public function testSSHConfiguration()
    {

        $this->public_key = "/FAKEFILE";
        $this->setExpectedException(\mm0\ImageManager\Exceptions\FileNotFoundException::class);
        $this->setupSSHConfiguration();

        $this->private_key = "/FAKEFILE";
        $this->setupSSHConfiguration();
    }

    public function testSSHFileNotReadable(){
        $this->public_key = "/root/";
        $this->setExpectedException(\mm0\ImageManager\Exceptions\FileNotReadableException::class);
        $this->setupSSHConfiguration();
    }
    public function testSSHConfigurationParams()
    {
        $this->assertEquals($this->ip_address, $this->ssh_configuration->host());
        $this->assertEquals($this->port, $this->ssh_configuration->port());
        $this->assertEquals($this->user, $this->ssh_configuration->user());
        $this->assertEquals($this->public_key, $this->ssh_configuration->publicKey());
        $this->assertEquals($this->private_key, $this->ssh_configuration->privateKey());
        $this->assertEquals($this->passphrase, $this->ssh_configuration->passphrase());
    }

}