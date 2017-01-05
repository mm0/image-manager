<?php

namespace mm0\ImageManager\Destinations;

use mm0\ImageManager\RecursiveDirectoryIterator;

class RecursiveDirectory extends AbstractDestination
{
    function returnIterator(){
        return RecursiveDirectoryIterator::class;
    }

}