<?php

namespace mm0\ImageManager\Destinations;

use mm0\ImageManager\FileIterator;

class File extends AbstractDestination
{
    function returnIterator(){
        return FileIterator::class;
    }

}