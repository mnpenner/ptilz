<?php
namespace Ptilz\Collections;

use ArrayIterator;
use IteratorAggregate;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Iter;
use Ptilz\V;

class Set implements IteratorAggregate {
    private $set;

    public function __construct($iter = null) {
        if($iter) {
            if($iter instanceof Set) {
                $this->set = $iter->set;
            } elseif(is_array($iter)) {
                $this->set = self::fill($iter);
            } elseif(Iter::isIterable($iter)) {
                $this->set = array();
                foreach($iter as $i) {
                    $this->set[$i] = true;
                }
            } else {
                throw new ArgumentTypeException(__CLASS__ . ' must be initialized with an array or iterable; ' . V::getType($iter) . ' provided');
            }
        } else {
            $this->set = array();
        }
    }

    public function contains($x) {
        return isset($this->set[$x]);
    }

    public function add($x) {
        $this->set[$x] = true;
    }

    // todo: rename to unionWith? https://msdn.microsoft.com/en-us/library/bb342097.aspx
    public function addRange($x) {
        foreach($x as $i) {
            $this->set[$i] = true;
        }
    }

    public function remove($x) {
        unset($this->set[$x]);
    }

    private static function fill($a) {
        return array_fill_keys($a, true);
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
            return new Set(array_keys(array_intersect_key($this->set, self::fill($x))));
        }
        throw new ArgumentTypeException('Cannot intersect set with object of type ' . V::getType($x));
    }

    public function union($x) {
        if($x instanceof Set) {
            return new Set(array_keys(self::merge($this->set, $x->set)));
        } elseif(is_array($x)) {
            return new Set(array_keys(self::merge($this->set, self::fill($x))));
        }
        throw new ArgumentTypeException('Cannot union set with object of type ' . V::getType($x));
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

    function __debugInfo() {
        return $this->set;
    }


}