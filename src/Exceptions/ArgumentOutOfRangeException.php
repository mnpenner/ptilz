<?php

namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when an argument is incorrectly formatted.
 */
class ArgumentOutOfRangeException extends \DomainException {
    public function __construct($paramName, $details=null, $code = 0, Exception $previous = null) {
        $message = "Argument `$paramName` is outside of the valid range";
        if($details) {
            $message .= ': '.$details;
        }
        Exception::__construct($message, $code, $previous);
    }
}