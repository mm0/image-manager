<?php

namespace mm0\ImageManager\FileTypes;

use mm0\ImageManager\BaseClass;
use mm0\ImageManager\Log as Log;

/**
 * Class AbstractFileType
 * @package mm0\ImageManager\FileTypes
 */
abstract class AbstractFileType extends BaseClass implements FileTypeInterface
{
    protected static $MIME_TYPE = "";

    public static function isValidFile($filename, $resource){
        return self::validateFileType($filename, $resource);
    }
    /**
     * @param $resource
     */
    public static function validateFileType($filename, $resource)
    {
        $mime = self::getMimeType($filename,$resource);
        return self::validateFileUsingMimeInfo($mime);


        // TODO: Implement validateFileType() method.
    }
    private static function getMimeType($filename, $resource){
        $mime = finfo_file($resource,$filename);
        return $mime;
    }
    private static function validateFileUsingMimeInfo($mime_info){
        Log::logInfo("Mime-info for File: " . $mime_info);
        Log::logInfo("Checking if type: " . static::$MIME_TYPE);
        if(strlen(static::$MIME_TYPE)){
            if($mime_info == static::$MIME_TYPE){
                Log::logInfo("Valid type: " . static::$MIME_TYPE);
                return true;
            }
        }
        return false;
    }
}