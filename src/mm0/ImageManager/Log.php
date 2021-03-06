<?php

namespace mm0\ImageManager;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Class Log
 * @package mm0\ImageManager
 *
 */
class Log
{
    protected static $instances;

    protected static $array_of_bad_words = array();

    protected static $sanitize_string = "***REDACTED***";
    /**
     * Method to return the Monolog instance
     *
     * @return \Monolog\Logger[]
     */
    public static function getLogger()
    {
        if (! self::$instances) {
            self::configureLoggers();
        }

        return self::$instances;
    }
    /**
     * @var string
     */
    protected static $log_dir = "/var/log/ImageManager";

    /**
     * @var string
     */
    protected static $log_filename = "ImageManager.log";

    /**
     * @var string
     */
    public static $log_level = "ERROR";

    /**
     * @return string
     */
    public static function getLogLevel()
    {
        return static::$log_level;
    }

    /**
     * @param string $log_level
     */
    public static function setLogLevel($log_level)
    {
        self::$log_level = $log_level;
    }
    public static function addWordFilter($word){
        static::$array_of_bad_words[] = $word;
    }

    /**
     * @param $message
     * @return mixed
     */
    public static function sanitize($message)
    {
        if (count(static::$array_of_bad_words))
            return str_replace(static::$array_of_bad_words, self::$sanitize_string, $message);
        else
            return $message;
    }
    /**
     * @param $message
     */
    public static function logInfo($message,$data=array())
    {
        self::log($message, "INFO",$data=array());
    }

    /**
     * @param $message
     */
    public static function logDebug($message,$data=array())
    {
        self::log($message, "DEBUG",$data=array());
    }

    /**
     * @param $message
     */
    public static function logError($message,$data=array())
    {
        self::log($message, "ERROR",$data=array());
    }

    /**
     * @param $message
     * @param string $severity
     */
    public static function logWarning($message,$data=array())
    {
        self::log($message, "WARNING",$data=array());

    }

    /**
     * @param $message
     * @param string $severity
     */
    public static function logNotice($message,$data=array())
    {
        self::log($message, "NOTICE",$data=array());

    }
    /**
     * @param $message
     * @param string $severity
     */
    public static function logCritical($message,$data=array())
    {
        self::log($message, "CRITICAL",$data=array());

    }
    /**
     * @param $message
     * @param string $severity
     */
    public static function logAlert($message,$data=array())
    {
        self::log($message, "ALERT",$data=array());

    }
    /**
     * @param $message
     * @param string $severity
     */
    public static function logEmergency($message,$data=array())
    {
        self::log($message, "EMERGENCY",$data);

    }
    /**
     * Configure Monolog to use a rotating files system.
     *
     * @return void
     */
    protected static function configureLoggers()
    {
        // the default date format is "Y-m-d H:i:s"
        $dateFormat = "Y-m-d H:i:s";
// the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        $output = "[%datetime%][%level_name%]: %message% \n";
// finally, create a formatter
        $formatter = new LineFormatter($output, $dateFormat);
        $logger = new Logger('mm0\ImageManager');
        $handler = new RotatingFileHandler(self::$log_dir . DIRECTORY_SEPARATOR . self::$log_filename, 5, static::$log_level);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        $log = new Logger('mm0\ImageManager');
        $handler = new StreamHandler("php://stderr", static::$log_level);
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);
        self::$instances = [
            $logger,
            $log
        ];
    }
    /**
     * @param $message
     * @param string $severity
     */
    public function log($message, $severity = "INFO", $data = null)
    {
        if (strlen($message)) {
            $function = "add".ucfirst($severity);
            foreach(self::getLogger() as $logger){
                $logger->$function(self::sanitize($message),$data);
            }
        }
    }


}