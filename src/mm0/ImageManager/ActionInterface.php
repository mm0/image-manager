<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 1/3/17
 * Time: 12:28 AM
 */

namespace mm0\ImageManager;


interface ActionInterface
{
    static function resourceAction(ConnectionInterface $connection, $resource);
}