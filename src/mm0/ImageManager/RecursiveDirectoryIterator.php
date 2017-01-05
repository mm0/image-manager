<?php

namespace mm0\ImageManager;


class RecursiveDirectoryIterator extends \RecursiveIteratorIterator
{
    private $directory;

    public function __construct($directory)
    {
        // create FilesystemIterator for base dir of file
        $iterator = new \RecursiveDirectoryIterator($directory);
        $filter = new RecursiveDirectoryFilterIterator($iterator);
        $this->directory = $directory;
        parent::__construct($filter);
        $this->rewind();
    }
    public function isDot(){
        return false;
    }
}

class RecursiveDirectoryFilterIterator extends \RecursiveFilterIterator {

    public function accept()
    {
        $filename = $this->current()->getFilename();
        // Skip hidden files and directories.
        if ($filename[0] === '.') {
            return FALSE;
        }
        return true;
    }
}