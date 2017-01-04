<?php

abstract class AbstractFullTestSuite extends PHPUnit_Framework_TestCase
{
    /**
     * @var \mm0\ImageManager\ConnectionInterface
     */
    protected $connection;
    /**
     * @var array
     */
    protected $save_modules = array();
        /**
     * @var string
     */
    protected $encryption_algorithm;
    /**
     * @var string
     */
    protected $encryption_key;
    /**
     * @var string
     */
    protected $save_directory;
    /**
     * @var int
     */
    protected $parallel_threads;

    /**
     * @var string
     */
    protected $host = "127.0.0.1";
    /**
     * @var int
     */
    protected $port = 22;
    /**
     * @var bool
     */
    protected $compress;
    /**
     * @var string
     */
    protected $user = "vagrant";
    /**
     * @var string
     */
    protected $passphrase = '';
    /**
     * @var string
     */
    protected $public_key_file = "/home/vagrant/.ssh/id_rsa.pub";
    /**
     * @var string
     */
    protected $private_key_file = "/home/vagrant/.ssh/id_rsa";
    /**
     * @var array
     */
    protected $ssh_options;
    protected $hostkey = "ssh-rsa";

    protected $bucket = "innobackup-testing-bucket";
    protected $region = "us-west-1";
    protected $concurrency = 16;

    /**
     * @var \mm0\ImageManager\SSH\Configuration
     */
    protected $ssh_configuration;
    /**
     *
     */
    public function setUp()
    {


    }

    /**
     *
     */
    public function tearDown()
    {
        $this->connection = null;
        $this->save_modules = null;
    }



    /**
     *
     */
    abstract protected function createConnection();

    abstract protected function setupSaveModules();


}