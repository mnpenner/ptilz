<?php

namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when one of the arguments provided to a method is not valid.
 * @seealso http://php.net/manual/en/class.logicexception.php
 */
class ArgumentException extends Exception {
    public function __construct($paramName, $details=null, $code = 0, Exception $previous = null) {
        $message = "Argument `$paramName` had an invalid value";
        if($details) {
            $message .= ': '.$details;
        }
        parent::__construct($message, $code, $previous);
    }
}