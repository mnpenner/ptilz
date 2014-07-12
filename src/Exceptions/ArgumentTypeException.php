<?php
namespace Ptilz\Exceptions;

use Exception;
use Ptilz\Arr;

/**
 * Thrown when an argument is of the wrong type.
 */
class ArgumentTypeException extends ArgumentException {
    public function __construct($paramName, $expectedType = null, $code = 0, Exception $previous = null) {
        $message = "Argument `$paramName` was not of the expected type";
        if($expectedType) {
            if(is_array($expectedType)) {
                $types = $expectedType;
            } else {
                $types = explode('|', (string)$expectedType);
            }
            $message .= ' ' . Arr::readable(array_map(function ($t) {
                    return "`" . trim($t) . "`";
                }, $types), ' or ');
        }
        parent::__construct($message, $code, $previous);
    }
}