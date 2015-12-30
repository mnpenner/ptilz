<?php namespace Ptilz;

use Ptilz\Exceptions\ArgumentTypeException;
use Traversable;

class Enumerable implements \IteratorAggregate {
    /** @var array|Traversable */
    private $foreachable;

    /**
     * @param array|Traversable $foreachable
     * @throws \Ptilz\Exceptions\ArgumentTypeException
     */
    public function __construct($foreachable) {
        Iter::assert($foreachable);
        $this->foreachable = $foreachable;
    }

    public function getIterator() {
        return Iter::toIterator($this->foreachable);
    }

    public function toArray() {
        return Iter::toArray($this->foreachable);
    }
}