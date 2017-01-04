<?php

namespace mm0\ImageManager\Destinations;

use mm0\ImageManager\DestinationInterface;

abstract class AbstractDestination implements DestinationInterface
{
    protected $path = "";

    /**
     * AbstractDestination constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    function returnIterator(){
        return $this->path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


}