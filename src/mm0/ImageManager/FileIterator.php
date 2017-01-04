<?php

namespace mm0\ImageManager;


class FileIterator extends \FilterIterator
{
    private $file_name;

    public function __construct($pathname)
    {
        // create FilesystemIterator for base dir of file
        $pathinfo = pathinfo($pathname);
        $dir = $pathinfo['dirname'];
        $file_name = $pathinfo['basename'];
        $iterator = new \DirectoryIterator($dir);
        $this->file_name = $file_name;
        parent::__construct($iterator);
        $this->rewind();
    }

    public function accept()
    {
        $file = $this->getInnerIterator()->current()->getFilename();
        if( strcasecmp($file,$this->file_name) == 0) {
            return true;
        }
        return false;
    }

    public function isDot(){
        return false;
    }
}