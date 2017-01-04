<?php

namespace mm0\ImageManager\Exceptions;

/**
 * Class SSH2ConnectionException
 * @package mm0\ImageManager\Exceptions
 */
class SSH2ConnectionException extends \Exception
{
    /**
     * SSH2ConnectionException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }


}