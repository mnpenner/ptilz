<?php

namespace Ptilz\Exceptions;
use Exception;

/**
 * Thrown when a method call is invalid for the object's current state.
 */
class InvalidOperationException extends Exception {}