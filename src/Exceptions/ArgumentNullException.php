<?php
namespace Ptilz\Exceptions;

use Ptilz\Exceptions\ArgumentException;

/**
 * Thrown when a null reference is passed to a method that does not accept it as a valid argument.
 */
class ArgumentNullException extends ArgumentException {}