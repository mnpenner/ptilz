<?php

namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when one of the arguments provided to a method is not valid.
 * @seealso http://php.net/manual/en/class.logicexception.php
 */
class ArgumentException extends Exception {
    public function __construct($paramName, $details=null, $code = 0, ?Exception $previous = null) {
        $label = (is_string($paramName) && $paramName !== '') ? " `$paramName`" : '';
        $message = "Argument{$label} had an invalid value";
        if($details !== null && $details !== '') {
            $message .= ': '.$details;
        }
        parent::__construct($message, $code, $previous);
    }
}