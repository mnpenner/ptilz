<?php
namespace Ptilz;

use Generator;
use Traversable;

class Iter {
    /**
     * Applies a callback to the given traversable as it is iterated.
     *
     * @param Traversable $trav
     * @param callable $callback
     * @return Generator
     */
    public static function map(Traversable $trav, callable $callback) {
        foreach($trav as $key => $val) {
            yield $callback($val, $key); // fixme: i think this might be yielding generators instead of values...
        }
    }

    public static function isIterable($obj) {
        return is_array($obj) || $obj instanceof Traversable;
    }
}