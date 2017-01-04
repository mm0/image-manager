<?php

namespace Connections;

/**
 * Class LocalShellConnectionTest
 */
class LocalShellConnectionTest extends \AbstractConnectionTest
{
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
    }

    /**
     *
     */
    public function createConnection()
    {
        $this->connection = new \mm0\ImageManager\LocalShell\Connection();
        $this->connection->setSudoAll(true);
    }

}