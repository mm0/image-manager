<?php

namespace mm0\ImageManager\Actions;

use mm0\ImageManager\ActionInterface;
use mm0\ImageManager\ConnectionInterface;
use mm0\ImageManager\Log;

class Delete implements ActionInterface
{
    public static function resourceAction(ConnectionInterface $connection, $resource){
        Log::logNotice("Delete Action for: " . $resource);

        $connection->rmfile($resource);
    }
}