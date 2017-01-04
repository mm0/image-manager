<?php

namespace mm0\ImageManager\Destinations;

class RecursiveDirectory extends AbstractDestination
{
    function returnIterator(){
        return \RecursiveDirectoryIterator::class;
    }

}