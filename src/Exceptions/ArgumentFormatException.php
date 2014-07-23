<?php

namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when an argument is incorrectly formatted.
 */
class ArgumentFormatException extends Exception {
    public function __construct($paramName, $details=null, $code = 0, Exception $previous = null) {
        $message = "Argument `$paramName` is not in the correct format";
        if($details) {
            $message .= ': '.$details;
        }
        Exception::__construct($message, $code, $previous);
    }
}