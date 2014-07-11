<?php
namespace Ptilz\Collections;

use ArrayIterator;
use IteratorAggregate;
use Ptilz\Exceptions\ArgumentTypeException;
use Traversable;

class Set implements IteratorAggregate {
    private $set;

    public function __construct($iter = null) {
        if($iter) {
            if($iter instanceof Set) {
                $this->set = $iter->set;
            } elseif(is_array($iter)) {
                $this->set = self::flip($iter);
            } elseif(self::isIter($iter)) {
                $this->set = array();
                foreach($iter as $i) {
                    $this->set[$i] = true;
                }
            } else {
                throw new ArgumentTypeException(__CLASS__ . ' must be initialized with an array or iterable; ' . self::getType($iter) . ' provided');
            }
        } else {
            $this->set = array();
        }
    }

    private static function getType($var) {
        return is_object($var) ? get_class($var) : gettype($var);
    }

    public function contains($x) {
        return isset($this->set[$x]);
    }

    public function add($x) {
        $this->set[$x] = true;
    }

    public function addRange($x) {
        foreach($x as $i) {
            $this->set[$i] = true;
        }
    }

    public function remove($x) {
        unset($this->set[$x]);
    }

    private static function flip($a) {
        return array_fill_keys($a, true);
    }

    private static function isIter($x) {
        return is_array($x) || $x instanceof Traversable;
    }

    private static function merge() {
        $arrays = func_get_args();
        $result = array_shift($arrays);
        foreach($arrays as $a) {
            foreach($a as $x => $_) {
                $result[$x] = true;
            }
        }
        return $result;
    }

    public function intersect($x) {
        if($x instanceof Set) {
            return new Set(array_keys(array_intersect_key($this->set, $x->set)));
        } elseif(is_array($x)) {
            return new Set(array_keys(array_intersect_key($this->set, self::flip($x))));
        }
        throw new ArgumentTypeException('Cannot intersect set with object of type ' . self::getType($x));
    }

    public function union($x) {
        if($x instanceof Set) {
            return new Set(array_keys(self::merge($this->set, $x->set)));
        } elseif(is_array($x)) {
            return new Set(array_keys(self::merge($this->set, self::flip($x))));
        }
        throw new ArgumentTypeException('Cannot union set with object of type ' . self::getType($x));
    }

    public function count() {
        return count($this->set);
    }

    public function toArray() {
        return array_keys($this->set);
    }

    public function getIterator() {
        return new ArrayIterator(array_keys($this->set));
    }

    public function __toString() {
        return '{' . implode(', ', array_keys($this->set)) . '}';
    }
}