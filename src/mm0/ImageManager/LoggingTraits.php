<?php

namespace mm0\ImageManager;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class LoggingTraits
 * @package mm0\ImageManager
 *
 */
trait LoggingTraits
{
    /**
     * @param $message
     */
    public function logInfo($message)
    {
        $this->log($message, "INFO");
    }

    /**
     * @param $message
     */
    public function logDebug($message)
    {
        $this->log($message, "DEBUG");
    }

    /**
     * @param $message
     */
    public function logError($message)
    {
        $this->log($message, "ERROR");
    }

    /**
     * @param $message
     * @param string $severity
     */
    public function logWarning($message)
    {
        $this->log($message, "WARNING");

    }
    /**
     * @param $message
     * @param string $severity
     */
    public function log($message, $severity = "INFO")
    {
        if(strlen($message)) {
            $log = new Logger('mm0\ImageManager');
            $log->pushHandler(new StreamHandler("php://stderr",Logger::EMERGENCY));
            $log->pushHandler(new StreamHandler("/dev/null",Logger::DEBUG));
            $log->log(Logger::toMonologLevel($severity), self::sanitize($message));
        }
    }

    public function sanitize($message){
        $sanitize_string = "***REDACTED***";
        if(property_exists($this,"array_of_bad_words") && is_array($this->array_of_bad_words) && count($this->array_of_bad_words))
            return str_replace($this->array_of_bad_words, $sanitize_string,$message);
        else
            return $message;
    }
}