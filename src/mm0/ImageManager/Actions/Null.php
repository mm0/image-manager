<?php

namespace mm0\ImageManager\Actions;

use mm0\ImageManager\ActionInterface;
use mm0\ImageManager\ConnectionInterface;

class Null implements ActionInterface
{
    public static function resourceAction(ConnectionInterface $connection, $resource){
        return; // do nothing
    }
}