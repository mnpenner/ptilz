<?php

namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when one of the arguments provided to a method is not valid.
 */
class ArgumentException extends Exception {
    public function __construct($paramName, $description=null, $code = 0, Exception $previous = null) {
        $message = "Argument `$paramName` had an invalid value";
        if($description) {
            $message .= ': '.$description;
        }
        parent::__construct($message, $code, $previous);
    }
}