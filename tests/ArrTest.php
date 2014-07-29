<?php
use Ptilz\Arr;

class ArrTest extends PHPUnit_Framework_TestCase {
    function testGet() {
        $arr = ['a' => 1, 2 => 'b'];
        $this->assertSame(1, Arr::get($arr, 'a'));
        $this->assertSame(1, Arr::get($arr, 'a', 'x'));
        $this->assertSame('b', Arr::get($arr, 2, 'x'));
        $this->assertSame('x', Arr::get($arr, 3, 'x'));
        $this->assertSame(null, Arr::get($arr, 'c'));
        $this->assertSame(4, Arr::get(['a'=>['b'=>['c'=>4]]], ['a','b','c']));
    }

    function testIsNumeric() {
        $this->assertTrue(Arr::isNumeric(['a', 'b']));
        $this->assertTrue(Arr::isNumeric([0 => 'a', 1 => 'b']));
        $this->assertFalse(Arr::isNumeric([1 => 'a', 2 => 'b']));
        $this->assertFalse(Arr::isNumeric([1 => 'b', 0 => 'a']));
        $this->assertFalse(Arr::isNumeric(['a' => 1]));
        $this->assertTrue(Arr::isNumeric([]));
    }

    function testIsAssoc() {
        $this->assertFalse(Arr::isAssoc(['a', 'b']));
        $this->assertFalse(Arr::isAssoc([0 => 'a', 1 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 2 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'b', 0 => 'a']));
        $this->assertTrue(Arr::isAssoc(['a' => 1]));
        $this->assertFalse(Arr::isAssoc([]));
    }

    public static function isOdd($val) {
        return ($val & 1) === 1;
    }

    public static function isPrivate($_, $key) {
        return is_string($key) && strlen($key) >= 1 && $key[0] === '_';
    }

    function testRemove() {
        $this->assertSame([2, 4, 6], Arr::remove([1, 2, 3, 4, 5, 6], ['ArrTest', 'isOdd']));
        $this->assertSame(['b' => 2], Arr::remove(['a' => 1, 'b' => 2], ['ArrTest', 'isOdd']));
        $this->assertSame(['username' => 'mark'], Arr::remove(['username' => 'mark', '_password' => 'secret'], ['ArrTest', 'isPrivate']));
    }

    function testFilter() {
        $this->assertSame([1, 3, 5], Arr::filter([1, 2, 3, 4, 5, 6], ['ArrTest', 'isOdd']), "Filter by value");
        $this->assertSame(['a' => 1], Arr::filter(['a' => 1, 'b' => 2], ['ArrTest', 'isOdd']), "Filter, maintaining keys");
        $this->assertSame(['_foo' => 'bar'], Arr::filter(['_foo' => 'bar', 'baz'], ['ArrTest', 'isPrivate']), "Filter by key");
        $this->assertSame(['0', "\0", -1, 1, true, [0]], Arr::filter(['', '0', "\0", -1, 0, 1, true, false, null, [], [0]]), "Default filter");
    }

    function testFirstValue() {
        $arr = [1, 2, 3];
        $dict = ['a' => 1, 'b' => 2];
        $this->assertSame(1, Arr::firstValue($arr));
        $this->assertSame(1, Arr::firstValue($dict));
    }

    function testFirstKey() {
        $arr = [1, 2, 3];
        $dict = ['a' => 1, 'b' => 2];
        $this->assertSame(0, Arr::firstKey($arr));
        $this->assertSame('a', Arr::firstKey($dict));
    }

    function testLastValue() {
        $arr = [1, 2, 3];
        $dict = ['a' => 1, 'b' => 2];
        $this->assertSame(3, Arr::lastValue($arr));
        $this->assertSame(2, Arr::lastValue($dict));
    }

    function testLastKey() {
        $arr = [1, 2, 3];
        $dict = ['a' => 1, 'b' => 2];
        $this->assertSame(2, Arr::lastKey($arr));
        $this->assertSame('b', Arr::lastKey($dict));
    }

    protected static $people = [
        [
            'name' => 'Steve',
            'age' => 36,
            'gender' => 'Male',
        ],
        [
            'name' => 'Susan',
            'age' => 18,
            'gender' => 'Female',
        ],
    ];

    function testRekey() {
        $this->assertSame([
            'Steve' => [
                'name' => 'Steve',
                'age' => 36,
                'gender' => 'Male',
            ],
            'Susan' => [
                'name' => 'Susan',
                'age' => 18,
                'gender' => 'Female',
            ]
        ], Arr::rekey(self::$people, 'name'));

        $this->assertSame([
            'Steve' => [
                'age' => 36,
                'gender' => 'Male',
            ],
            'Susan' => [
                'age' => 18,
                'gender' => 'Female',
            ]
        ], Arr::rekey(self::$people, 'name', true));
    }

    function testPluck() {
        $this->assertSame(['Steve', 'Susan'], Arr::pluck(self::$people, 'name'));
        $this->assertSame(['a' => 'a2', 'b' => 'b2'],
            Arr::pluck([
                'a' => ['k1' => 'a1', 'k2' => 'a2'],
                'b' => ['k1' => 'b1', 'k2' => 'b2'],
            ], 'k2'));
    }

    function testKeys() {
        $arr = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
        ];
        $this->assertSame(['b' => 2, 'd' => 4], Arr::only($arr, ['d', 'b']));
        $this->assertSame(['d' => 4, 'b' => 2], Arr::only($arr, ['d', 'b'], true));
    }

    function testPop() {
        $arr = [1,2,3,4,5,6];
        $this->assertSame(6,Arr::pop($arr));
        $this->assertSame([1,2,3,4,5],$arr);

        $arr = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
        ];
        $this->assertSame(4,Arr::pop($arr,'d'));
        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'e' => 5,
        ],$arr);
    }

    function testKeysUnion() {
        $this->assertSame(['a', 'b', 'c'], Arr::keysUnion(['a' => 1, 'b' => 2], ['b' => 2, 'c' => 3]));
        $this->assertSame([0, 1, 2, 3], Arr::keysUnion([1, 2, 3], ['a', 'b', 'c'], ['W', 'X', 'Y', 'Z']));
    }

    function testKeysIntersection() {
        $this->assertSame(['b'], Arr::keysIntersection(['a' => 1, 'b' => 2], ['b' => 2, 'c' => 3]));
        $this->assertSame([0, 1, 2], Arr::keysIntersection([1, 2, 3], ['a', 'b', 'c'], ['W', 'X', 'Y', 'Z']));
    }

    function testZip() {
        $this->assertSame([
            [1, 'a', 'W'],
            [2, 'b', 'X'],
            [3, 'c', 'Y'],
            [null, null, 'Z']
        ], Arr::zip(
            [1, 2, 3],
            ['a', 'b', 'c'],
            ['W', 'X', 'Y', 'Z']
        ));
    }

    function testConcat() {
        $this->assertSame([1, 2, 3, 3, 4, 5], Arr::concat(['a' => 1, 'b' => 2, 'c' => 3], [3, 4, 5]));
    }

    function testMerge() {
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3, 0 => 3, 1 => 4, 2 => 5], Arr::merge(['a' => 1, 'b' => 2, 'c' => 3], [3, 4, 5]));
        $this->assertSame([3, 4, 5, 'z'], Arr::merge([1, 2, 3, 'z'], [3, 4, 5]));
    }

    function testExtend() {
        $arr = [1, 2, 3];
        $this->assertSame([1, 2, 3, 3, 4, 5], Arr::extend($arr, [3, 4, 5]));
        $this->assertSame([1, 2, 3, 3, 4, 5], $arr);

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        Arr::extend($arr, ['c' => 4, 'd' => 5]);
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 4, 'd' => 5], $arr);
    }

    function testRegroup() {
        $people = [
            [
                'name' => 'Brett',
                'age' => 26,
                'gender' => 'Male',
            ],
            [
                'name' => 'Courtenay',
                'age' => 25,
                'gender' => 'Female',
            ],
            [
                'name' => 'Julia',
                'age' => 30,
                'gender' => 'Female',
            ],
        ];

        $this->assertSame([
            'Male' => [
                [
                    'name' => 'Brett',
                    'age' => 26,
                    'gender' => 'Male',
                ],
            ],
            'Female' => [
                [
                    'name' => 'Courtenay',
                    'age' => 25,
                    'gender' => 'Female',
                ],
                [
                    'name' => 'Julia',
                    'age' => 30,
                    'gender' => 'Female',
                ],
            ]
        ], Arr::regroup($people, 'gender', false, false), "Regroup by gender");

        $this->assertSame([
            'Male' => [
                [
                    'name' => 'Brett',
                    'age' => 26,
                ],
            ],
            'Female' => [
                [
                    'name' => 'Courtenay',
                    'age' => 25,
                ],
                [
                    'name' => 'Julia',
                    'age' => 30,
                ],
            ]
        ], Arr::regroup($people, 'gender', true, false), "Regroup by gender, unsetting 'gender' key");

        $this->assertSame([
            'Brett' => [
                'age' => 26,
                'gender' => 'Male',
            ],
            'Courtenay' => [
                'age' => 25,
                'gender' => 'Female',
            ],
            'Julia' => [
                'age' => 30,
                'gender' => 'Female',
            ],
        ], Arr::regroup($people, 'name', true, true), "Regroup by name, unsetting 'name' key and flattening results");

        $this->assertSame([
            'Male' => [
                26 => [
                    ['name' => 'Brett']
                ],
            ],
            'Female' => [
                25 => [
                    ['name' => 'Courtenay']
                ],
                30 => [
                    ['name' => 'Julia']
                ],
            ],
        ], Arr::regroup($people, ['gender', 'age'], true, false), "Regroup by name and age");

        $this->assertSame([
            'Male' => [
                26 => 'Brett',
            ],
            'Female' => [
                25 => 'Courtenay',
                30 => 'Julia',
            ],
        ], Arr::regroup($people, ['gender', 'age'], true, true), "Regroup by name and age, flatten");
    }

    function testZipdict() {
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], Arr::zipdict(['a', 'b', 'c'], [1, 2, 3]));
    }

    function testFlatten() {
        $this->assertSame([1, 2, 3, 4, 5, 6], Arr::flatten([[1, [], 2, 3], [4, [5], 6]]));
    }

    function testReadable() {
        $this->assertSame('', Arr::readable([]));
        $this->assertSame('A', Arr::readable(['A']));
        $this->assertSame('A and B', Arr::readable(['A', 'B']));
        $this->assertSame('A, B and C', Arr::readable(['A', 'B', 'C']));
        $this->assertSame('A, B or C', Arr::readable(['A', 'B', 'C'], ' or '));
        $this->assertSame('A; B or C', Arr::readable(['A', 'B', 'C'], ' or ', '; '));
        $this->assertSame('A or B', Arr::readable(['A', 'B'], ' or ', '; ', true));
        $this->assertSame('A; B; or C', Arr::readable(['A', 'B', 'C'], ' or ', '; ', true));
    }

    function testTranspose() {
        $mat = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
        ];
        $trans = [
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
        ];
        $this->assertSame($trans, Arr::transpose($mat));

        $mat = [
            'a' => [1 => 'a1', 2 => 'a2', 3 => 'a3'],
            'b' => [1 => 'b1', 2 => 'b2', 3 => 'b3'],
            'c' => [1 => 'c1', 2 => 'c2', 3 => 'c3']
        ];
        $trans = [
            1 => ['a' => 'a1', 'b' => 'b1', 'c' => 'c1'],
            2 => ['a' => 'a2', 'b' => 'b2', 'c' => 'c2'],
            3 => ['a' => 'a3', 'b' => 'b3', 'c' => 'c3'],
        ];
        $this->assertSame($trans, Arr::transpose($mat));
    }

    function testMap() {
        $timesTwo = function ($x) {
            return $x * 2;
        };
        $this->assertSame([2, 4, 6], Arr::map([1, 2, 3], $timesTwo), "Basic usage");

        $generator = function () {
            yield 1;
            yield 2;
            yield 3;
        };
        $this->assertSame([2, 4, 6], Arr::map($generator(), $timesTwo), "Use map on a generator");

        $times2and3 = function ($v, $k) {
            yield $v * 2;
            yield $v * 3;
        };
        $this->assertSame([2, 3, 4, 6, 6, 9], Arr::map([1, 2, 3], $times2and3), "Use map to generate more than one value");

        $letterMap = function ($v, $k) {
            yield chr(97 + $k) => $v;
        };
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], Arr::map([1, 2, 3], $letterMap), "Use map to change keys");

        $isOdd = function ($v) {
            if(($v & 1) === 1) {
                yield $v;
            }
        };
        $this->assertSame([1, 3, 5], Arr::map([1, 2, 3, 4, 5, 6], $isOdd), "Use map as a filter");
    }

    function testWrap() {
        $this->assertSame(['<b>', '<i>', '<u>'], Arr::wrap(['b', 'i', 'u'], '<', '>'));
        $this->assertSame('<b><i><u>', Arr::wrap(['b', 'i', 'u'], '<', '>', ''));
        $this->assertSame('[a],[b],[c]', Arr::wrap(['a', 'b', 'c'], '[', ']', ','));
    }

    function testInc() {
        $arr = ['a' => 1];
        Arr::inc($arr, 'a');
        $this->assertSame(['a' => 2], $arr);
        Arr::inc($arr, 'b', 2);
        $this->assertSame(['a' => 2, 'b' => 2], $arr);
    }
}
