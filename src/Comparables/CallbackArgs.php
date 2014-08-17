<?php
namespace Ptilz\Comparables;

class CallbackArgs {
    private $callback;
    private $args;

    /**
     * @param callable $callable Callable function
     * @param mixed ...$arg Arguments to pass to callable
     */
    function __construct() {
        $args = func_get_args();
        $this->callback = array_shift($args);
        $this->args = $args;
    }

    function __invoke(&$arg) {
        return call_user_func_array($this->callback, array_merge(array(&$arg), $this->args));
    }
}