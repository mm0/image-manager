<?php

namespace mm0\ImageManager\Destinations;

class Directory extends AbstractDestination
{
    function returnIterator(){
        return \DirectoryIterator::class;
    }

}