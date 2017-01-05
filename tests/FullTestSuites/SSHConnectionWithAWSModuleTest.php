<?php

namespace FullTestSuites;
/**
 * Class SSHConnectionWithAWSModuleTest
 */
class SSHConnectionWithAWSModuleTest extends \AbstractFullTestSuite
{
    /**
     *
     */
    public function createConnection()
    {
        $this->ssh_configuration = new \mm0\ImageManager\Configuration\SSH(
            $this->host,
            $this->port,
            $this->user,
            $this->public_key_file,
            $this->private_key_file,
            $this->passphrase,             // ssh key passphrase
            array('hostkey' => $this->hostkey)
        );
        $this->connection = new \mm0\ImageManager\SSH\Connection(
            $this->ssh_configuration
        );
        $this->connection->setSudoAll(true);
    }

    public function tearDown(){
        $this->mysql_config = null;
        $this->connection = null;
        $this->ssh_configuration = null;
    }
    public function setupSaveModules()
    {
        $this->save_modules = array(
            new \mm0\ImageManager\AWS\CLIUploader(
                $this->connection,
                $this->bucket,
                $this->region,
                $this->concurrency
            )
        );
    }

    public function testBucketNotExists(){
        $this->setExpectedException(\mm0\ImageManager\Exceptions\BucketNotFoundException::class);
        $this->createConnection();

        $this->bucket = "fakebucket";
        new \mm0\ImageManager\AWS\CLIUploader(
            $this->connection,
            $this->bucket,
            $this->region,
            $this->concurrency
        );
    }

    public function testCliChecker(){
        $this->setExpectedException(\mm0\ImageManager\Exceptions\CLINotFoundException::class);
        $this->createConnection();

        $a = new \mm0\ImageManager\AWS\CLIUploader(
            $this->connection,
            $this->bucket,
            $this->region,
            $this->concurrency
        );;
        $reflection = new \ReflectionClass($a);
        $reflection_property = $reflection->getProperty('binary');
        $reflection_property->setAccessible(true);

        $reflection_property->setValue($a, "fakebinary_aws_cli");
        $a->testSave();

    }

}