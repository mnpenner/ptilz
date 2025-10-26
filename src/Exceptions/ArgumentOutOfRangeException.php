<?php

namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when an argument is incorrectly formatted.
 */
class ArgumentOutOfRangeException extends \DomainException {
    public function __construct($paramName, $details=null, $code = 0, ?Exception $previous = null) {
        $label = (is_string($paramName) && $paramName !== '') ? " `$paramName`" : '';
        $message = "Argument{$label} is outside of the valid range";
        if($details !== null && $details !== '') {
            $message .= ': '.$details;
        }
        parent::__construct($message, $code, $previous);
    }
}