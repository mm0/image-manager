<?php

namespace mm0\ImageManager\FileTypes;


interface FileTypeInterface
{
    static function validateFileType($filename, $resource);
}