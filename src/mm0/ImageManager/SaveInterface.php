<?php

namespace mm0\ImageManager;


/**
 * Interface SaveInterface
 * @package mm0\ImageManager
 */
interface SaveInterface
{

    /**
     * @param string $filename
     * @return mixed
     */
    public function save($filename);
    /**
     * @param string $filename
     * @return mixed
     */
    public function saveFile($filename);
    /**
     * @param string $filename
     * @return mixed
     */
    public function saveDirectory($filename);

    /**
     * @return mixed
     */
    public function testSave();

    /**
     * @param mixed $key
     */
    public function setKey($key);

}