<?php

abstract class AbstractConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \mm0\ImageManager\ConnectionInterface
     */
    protected $connection;

    /**
     *
     */
    public function setUp()
    {
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->connection = null;
    }

    abstract protected function createConnection();


    public function testConnection()
    {
        $this->createConnection();
        $this->connection->verify();

        $this->connection->setSudoAll(false);
        $this->assertInstanceOf(\mm0\ImageManager\ConnectionInterface::class, $this->connection);
        $command = "whoami";
        $this->assertFalse($this->connection->isSudoAll());
        $response = $this->connection->executeCommand($command);

        $this->assertInstanceOf(\mm0\ImageManager\ConnectionResponse::class, $response);
        $this->assertEquals($command, $response->command());

        // Assuming testing in vagrant rather than elsewhere
        $this->assertEquals("vagrant", $response->stdout());

        $this->connection->setSudoAll(true);
        $this->assertTrue($this->connection->isSudoAll());

        $response = $this->connection->executeCommand($command);
        $this->assertEquals("sudo " . $command, $response->command());
        $this->assertEquals("root", $response->stdout());

        // explicit test of ConnectionResponse::showOutput()
        $response->showOutput();

        $this->connection->setSudoAll(false);
        $random = substr(md5(rand()), 0, 7);
        $contents = "unit_test" . $random;
        $tmp_file = $this->connection->getTemporaryDirectoryPath() . $contents;
        $result = $this->connection->writeFileContents($tmp_file, $contents);
        $this->assertTrue($result);

        $this->assertTrue($this->connection->file_exists($tmp_file));

        $file_contents = $this->connection->getFileContents($tmp_file);
        $this->assertEquals($contents, $file_contents);

        $scan = $this->connection->scandir($this->connection->getTemporaryDirectoryPath());
        $this->assertNotFalse($scan);

        $this->assertTrue($this->connection->mkdir($tmp_file . "dir"));
    }

    public function testRemoveDirectory(){
        $this->createConnection();

        $directory = $this->connection->getTemporaryDirectoryPath(). "/fakedir";
        $this->assertFalse($this->connection->file_exists($directory));
        $this->connection->mkdir($directory);
        $this->assertTrue($this->connection->file_exists($directory));
        $this->connection->rmdir($directory);
        $this->assertFalse($this->connection->file_exists($directory));
    }

    public function testGetConnection(){
        $this->createConnection();
        $this->assertInstanceOf(\mm0\ImageManager\ConnectionInterface::class,$this->connection->getConnection());

    }

    public function testRemoveFile(){
        $this->createConnection();
        $contents = "temp_file";
        $tmp_file = $this->connection->getTemporaryDirectoryPath() . $contents;
        $result = $this->connection->writeFileContents($tmp_file, $contents);
        $this->assertTrue($result);
        $this->assertTrue($this->connection->file_exists($tmp_file));
        $this->connection->rmfile($tmp_file);
        $this->assertFalse($this->connection->file_exists($tmp_file));


    }
    public function testFileNotExistsFileContents(){
        $this->createConnection();
        $this->assertEmpty($this->connection->getFileContents("/tmp/fakefilerandomtest"));
    }
}