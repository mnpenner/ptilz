<?php

namespace Ptilz\Exceptions;
use Exception;

/**
 * Thrown when an action is not supported, such as when a derived class can't implement a method.
 */
class NotSupportedException extends Exception {}
