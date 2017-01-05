<?php

namespace mm0\ImageManager;


abstract class AbstractConnection extends BaseClass implements ConnectionInterface
{
    /**
     * @var bool
     */
    protected $sudo_all = false;

    /**
     * @var \Iterator::class
     */
    protected $iterator;

    /**
     * @return \Iterator
     */
    public function getIteratorType()
    {
        return $this->iterator;
    }

    /**
     * @param \Iterator $iterator
     */
    public function setIteratorType($iterator)
    {
        $this->iterator = $iterator;
    }


    /**
     * @return boolean
     */
    public function isSudoAll()
    {
        return $this->sudo_all;
    }
    public function getIterator($path){
        $type = $this->getIteratorType();
        return new $type($path);
    }

    /**
     * @param string $directory
     * @return boolean
     */
    public function is_dir($directory)
    {
        return is_dir($directory);
    }
    /**
     * @param boolean $sudo_all
     */
    public function setSudoAll($sudo_all)
    {
        $this->sudo_all = $sudo_all;
    }


    public function getFileResource($file){
        return \finfo_open(FILEINFO_MIME_TYPE);

    }

    public function closeFileResource($resource){
        finfo_close($resource);
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection(){
        return $this;
    }
}