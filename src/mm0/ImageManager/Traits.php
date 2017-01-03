<?php
/**
 * Created by Matt Margolin.
 * Date: 3/28/16
 * Time: 1:29 PM
 */
namespace mm0\ImageManager;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait Traits
{
    use LoggingTraits;

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}