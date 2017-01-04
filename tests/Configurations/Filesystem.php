<?php

namespace Configurations;
/**
 * Class FilesystemTest
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \mm0\ImageManager\Configuration\Filesystem
     */
    private $Filesystem_configuration;


    public function setUp()
    {
    }

    /**
     *
     */
    public function tearDown()
    {
        $this->Filesystem_configuration = null;
    }


    private function setupFilesystemConfiguration()
    {
        $this->Filesystem_configuration = new \mm0\ImageManager\Configuration\Filesystem();
    }
    public function testAddFilesystemDestination()
    {

    }
}