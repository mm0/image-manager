<?php

namespace FullTestSuites;


class LocalConnectionWithAWSModuleTest extends \AbstractFullTestSuite
{
    /**
     *
     */
    public function createConnection()
    {
        $this->connection = new \mm0\ImageManager\LocalShell\Connection();
        $this->connection->setSudoAll(true);
    }

    public function setupSaveModules()
    {
        $this->save_modules = array(
            new \mm0\ImageManager\AWS\Uploader(
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
        new \mm0\ImageManager\AWS\Uploader(
            $this->connection,
            $this->bucket."fakebucket",
            $this->region,
            $this->concurrency
        );
    }

}