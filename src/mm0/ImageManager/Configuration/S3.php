<?php

namespace mm0\ImageManager\Configuration;

use mm0\ImageManager\BaseClass;

/**
 * Class S3
 * @package mm0\ImageManager\Configuration
 */
class S3 extends BaseClass
{

    /**
     * @var String
     */
    private $bucket;
    /**
     * @var String
     */
    private $region;


    /**
     * @param $region
     */
    public function setRegion($region){
        if($this->_validateRegion($region))
            $this->region = $region;
    }

    /**
     * @param $bucket
     */
    public function setBucket($bucket){
        $this->bucket = $bucket;
    }

    public function getRegion(){
        return $this->region;
    }

    public function getBucket(){
        return $this->bucket;
    }
    /**
     * @param $region
     * @return bool
     */
    private function _validateRegion($region){
        return true;
    }

    /**
     */
    public function __construct()
    {

    }


}