<?php
namespace Ptilz\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Iter;

class Set implements IteratorAggregate, Countable {
    private array $set = [];

    public function __construct($iter = null) {
        if($iter) {
            if($iter instanceof Set) {
                $this->set = $iter->set;
            } elseif(is_array($iter)) {
                $this->set = self::fill($iter);
            } elseif(Iter::isIterable($iter)) {
                $this->set = [];
                foreach($iter as $i) {
                    $this->set[$i] = true;
                }
            } else {
                throw new ArgumentTypeException('iter', [self::class, 'array', \Traversable::class]);
            }
        } else {
            $this->set = [];
        }
    }

    public function contains($x): bool {
        return isset($this->set[$x]);
    }

    public function add(...$value): void {
        foreach($value as $v) {
            $this->set[$v] = true;
        }
    }

    /**
     * @param array ...$range
     * @deprecated Replaced by `unionWith`
     * @see \Ptilz\Collections\Set::unionWith
     */
    public function addRange(...$range): void {
        foreach($range as $values) {
            foreach ($values as $v) {
                $this->set[$v] = true;
            }
        }
    }

    public function unionWith($x): void {
        if($x instanceof self) {
            $other = $x->set;
        } elseif(is_array($x)) {
            $other = self::fill($x);
        } else {
            throw new ArgumentTypeException('x', ['array',self::class]);
        }
        $this->set = array_replace($this->set, $other);
    }


    public function remove(...$value): void {
        foreach($value as $v) {
            unset($this->set[$v]);
        }
    }

    private static function fill($a): array {
        return array_fill_keys($a, true);
    }

    private static function merge(...$arrays): array {
        if(!$arrays) {
            return [];
        }
        return array_replace(...$arrays);
    }

    public function intersect($x): Set {
        if($x instanceof Set) {
            return new Set(array_keys(array_intersect_key($this->set, $x->set)));
        } elseif(is_array($x)) {
            return new Set(array_keys(array_intersect_key($this->set, self::fill($x))));
        }
        throw new ArgumentTypeException('x', ['array',self::class]);
    }

    public function union($x): Set {
        if($x instanceof self) {
            return new Set(array_keys(self::merge($this->set, $x->set)));
        } elseif(is_array($x)) {
            return new Set(array_keys(self::merge($this->set, self::fill($x))));
        }
        throw new ArgumentTypeException('x', ['array',self::class]);
    }

    public function count(): int {
        return count($this->set);
    }

    public function toArray(): array {
        return array_keys($this->set);
    }

    public function getIterator(): \Traversable {
        return new ArrayIterator($this->toArray());
    }

    public function __toString(): string {
        return '{' . implode(', ', array_keys($this->set)) . '}';
    }

    public function __debugInfo(): array {
        return $this->set;
    }
}
