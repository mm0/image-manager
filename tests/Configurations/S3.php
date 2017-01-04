<?php

namespace Configurations;
/**
 * Class S3Test
 */
class S3Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \mm0\ImageManager\Configuration\S3
     */
    private $s3_configuration;

    private $connection;

    public function setUp()
    {

    }

    /**
     *
     */
    public function tearDown()
    {
        $this->s3_configuration = null;
        $this->connection = null;
    }

    /**
     *
     */
    private function createS3ConfigurationObject()
    {
        $this->s3_configuration = new \mm0\ImageManager\Configuration\S3();
        $this->s3_configuration->setRegion("us-west-1");
        $this->s3_configuration->setBucket("image-manager-test-bucket");
    }

    /**
     *
     */
    public function testCreateMysqlConfigurationObject()
    {
        // Setup
        $this->createS3ConfigurationObject();

    }
//
//    public function testMysqlConnectionException(){
//        $this->setExpectedException(\mm0\ImageManager\Exceptions\MySQLConnectionException::class);
//        $this->mysql_port=22;
//        $this->createMySQLConfigurationObject();
//        $this->mysql_config->verify();
//    }

}