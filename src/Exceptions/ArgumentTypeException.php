<?php
namespace Ptilz\Exceptions;

use Exception;
use Ptilz\Arr;

/**
 * Thrown when an argument is of the wrong type.
 * @seealso http://php.net/manual/en/class.logicexception.php
 */
class ArgumentTypeException extends ArgumentException {
    public function __construct($paramName, $expectedType = null, $code = 0, ?Exception $previous = null) {
        $message = 'Argument ';
        if(is_string($paramName) && $paramName !== '') {
            $message .= "`$paramName` ";
        } elseif($paramName !== null) {
            $message .= '`' . (string)$paramName . '` ';
        }
        $message .= 'was not of the expected type';
        if($expectedType) {
            $types = is_array($expectedType) ? $expectedType : explode('|', (string)$expectedType);
            $message .= ' ' . Arr::readable(array_map(function ($t) {
                    return "`" . trim($t) . "`";
                }, $types), ' or ');
        }
        Exception::__construct($message, $code, $previous);
    }
}