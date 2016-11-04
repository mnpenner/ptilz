<?php
namespace Ptilz\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Iter;

class Set implements IteratorAggregate, Countable {
    private $set;

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

    public function contains($x) {
        return isset($this->set[$x]);
    }

    public function add(...$value) {
        foreach($value as $v) {
            $this->set[$v] = true;
        }
    }

    /**
     * @param array ...$range
     * @deprecated Replaced by `unionWith`
     * @see \Ptilz\Collections\Set::unionWith
     */
    public function addRange(...$range) {
        foreach($range as $values) {
            foreach ($values as $v) {
                $this->set[$v] = true;
            }
        }
    }

    public function unionWith($x) {
        if($x instanceof self) {
            $other = $x->set;
        } elseif(is_array($x)) {
            $other = self::fill($x);
        } else {
            throw new ArgumentTypeException('x', ['array',self::class]);
        }
        $this->set = array_replace($this->set, $other);
    }


    public function remove(...$value) {
        foreach($value as $v) {
            unset($this->set[$v]);
        }
    }

    private static function fill($a) {
        return array_fill_keys($a, true);
    }

    private static function merge(...$arrays) {
        if(!$arrays) {
            return [];
        }
        return array_replace(...$arrays);
    }

    public function intersect($x) {
        if($x instanceof Set) {
            return new Set(array_keys(array_intersect_key($this->set, $x->set)));
        } elseif(is_array($x)) {
            return new Set(array_keys(array_intersect_key($this->set, self::fill($x))));
        }
        throw new ArgumentTypeException('x', ['array',self::class]);
    }

    public function union($x) {
        if($x instanceof self) {
            return new Set(array_keys(self::merge($this->set, $x->set)));
        } elseif(is_array($x)) {
            return new Set(array_keys(self::merge($this->set, self::fill($x))));
        }
        throw new ArgumentTypeException('x', ['array',self::class]);
    }

    public function count() {
        return count($this->set);
    }

    public function toArray() {
        return array_keys($this->set);
    }

    public function getIterator() {
        return new ArrayIterator($this->toArray());
    }

    public function __toString() {
        return '{' . implode(', ', array_keys($this->set)) . '}';
    }

    function __debugInfo() {
        return $this->set;
    }


}