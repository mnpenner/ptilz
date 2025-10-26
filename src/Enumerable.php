<?php namespace Ptilz;

use Ptilz\Exceptions\ArgumentTypeException;
use Traversable;

class Enumerable implements \IteratorAggregate {
    private iterable $foreachable;

    /**
     * @param array|Traversable $foreachable
     * @throws \Ptilz\Exceptions\ArgumentTypeException
     */
    public function __construct($foreachable) {
        Iter::assert($foreachable);
        $this->foreachable = $foreachable;
    }

    public function getIterator(): \Traversable {
        return Iter::toIterator($this->foreachable);
    }

    public function toArray(): array {
        return Iter::toArray($this->foreachable);
    }
}
