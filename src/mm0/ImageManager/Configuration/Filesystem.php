<?php

namespace mm0\ImageManager\Configuration;

use mm0\ImageManager\BaseClass;
use mm0\ImageManager\DestinationInterface;

/**
 * Class Filesystem
 * @package mm0\ImageManager\Configuration
 */
class Filesystem extends BaseClass
{

    /**
     * @var DestinationInterface[]
     */
    private $destinations = array();
    /**
     * Filesystem constructor.
     */
    public function __construct($destinations = array())
    {
        foreach($destinations as $destination){
            $this->_addDestination($destination);
        }
    }

    /**
     * @param DestinationInterface $destination
     */
    private function _addDestination(DestinationInterface $destination){
        if(!in_array($destination, $this->destinations)){
            $this->destinations[] = $destination;
        }
    }

    /**
     * @param $destination
     */
    private function _validateDestination($destination){

    }

    /**
     * @param $directory
     */
    public function addImageDirectory($directory){

    }

    /**
     * @param DestinationInterface $filename
     */
    public function addDestination(DestinationInterface $filename){
        $this->_addDestination($filename);
    }

    /**
     * @return DestinationInterface[]
     */
    public function getDestinations(){
        return $this->destinations;
    }

}