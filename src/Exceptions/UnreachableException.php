<?php

namespace Ptilz\Exceptions;
use Exception;

/**
 * You should never see this exception.
 *
 * May be used to suppress IDE warnings such as when you declare a variable in a `switch` block, you can use this in the `default` case.
 */
class UnreachableException extends Exception {
    public function __construct($message = "Logic error; this code should be unreachable", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
