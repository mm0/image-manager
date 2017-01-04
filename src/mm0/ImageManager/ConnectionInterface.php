<?php

namespace mm0\ImageManager;

use phpDocumentor\Reflection\Types\Resource;

interface ConnectionInterface
{
    /**
     * @return ConnectionResponse
     */
    function executeCommand($command);

    /**
     * @param string $file
     * @return string
     */
    function getFileContents($file);

    /**
     * @param string $file
     * @param string $contents
     * @param int $mode
     * @return bool
     */
    function writeFileContents($file, $contents, $mode = 0644);

    /**
     * @param string $file
     * @return boolean
     */
    function file_exists($file);

    /**
     * @return resource
     */
    function getConnection();

    /**
     * @return boolean
     */
    function isSudoAll();

    /**
     * @param boolean
     */
    function setSudoAll($bool);

    function verify();

    /**
     * @param $directory
     * @return mixed
     */
    function scandir($directory);

    /**
     * @param $directory
     * @return boolean
     */
    function is_dir($directory);

    /**
     * @param $directory
     * @return mixed
     */
    function mkdir($directory);
    /**
     * @param $file
     * @return mixed
     */
    function rmfile($file);
    /**
     * @param $directory
     * @return mixed
     */
    function rmdir($directory);

    /**
     * @return mixed
     */
    function getTemporaryDirectoryPath();

    /**
     * @param string $iterator
     * @return void
     */
    function setIteratorType($iterator);

    /**
     * @return strin
     */
    function getIteratorType();

    /**
     * @param string $path
     * @return \Iterator
     */
    function getIterator($path);

    /**
     * @param $file
     * @return Resource
     */
    function getFileResource($file);

    function closeFileResource($resource);

}