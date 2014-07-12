<?php
namespace Ptilz\Exceptions;

use Exception;

/**
 * Thrown when a null/empty value is passed to a method that does not accept it as a valid argument.
 */
class ArgumentEmptyException extends ArgumentException {
    public function __construct($paramName, $code = 0, Exception $previous = null) {
        parent::__construct("Argument `$paramName` cannot be null", $code, $previous);
    }
}