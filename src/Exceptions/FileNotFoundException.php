<?php

namespace Ptilz\Exceptions;
use Exception;

/**
 * Thrown when a method call is invalid for the object's current state.
 */
class FileNotFoundException extends Exception {
    public function __construct($filename = "", $code = 0, Exception $previous = null) {
        parent::__construct("File not found: $filename", $code, $previous);
    }

}