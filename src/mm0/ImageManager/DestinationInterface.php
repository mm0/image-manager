<?php

namespace mm0\ImageManager;


/**
 * Interface DestinationInterface
 * @package mm0\ImageManager
 */
interface DestinationInterface
{
    /**
     * @return \Iterator
     */
    function returnIterator();

    /**
     * @return String
     */
    function getPath();
}