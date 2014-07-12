<?php

namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when an argument is incorrectly formatted.
 */
class ArgumentOutOfRangeException extends Exception {
    public function __construct($paramName, $details=null, $code = 0, Exception $previous = null) {
        $message = "Argument `$paramName` is outside of the valid range";
        if($details) {
            $message .= ': '.$details;
        }
        parent::__construct($message, $code, $previous);
    }
}