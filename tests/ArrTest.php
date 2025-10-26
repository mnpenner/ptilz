<?php
use Ptilz\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase {

    /**
     * @covers \Ptilz\Arr::get
     */
    function testGet() {
        $arr = ['a' => 1, 2 => 'b'];
        $this->assertSame(1, Arr::get($arr, 'a'));
        $this->assertSame(1, Arr::get($arr, 'a', 'x'));
        $this->assertSame('b', Arr::get($arr, 2, 'x'));
        $this->assertSame('x', Arr::get($arr, 3, 'x'));
        $this->assertSame(null, Arr::get($arr, 'c'));
        $this->assertSame(4, Arr::get(['a'=>['b'=>['c'=>4]]], ['a','b','c']));
        $this->assertSame(9, Arr::get(['foo'=>'bar','baz'=>'9'],'baz','quux','int'));
        $this->assertSame('quux', @Arr::get(['foo'=>'bar','baz'=>'9'],'baz','quux','INVALID_TYPE'));
    }

    /**
     * @covers \Ptilz\Arr::getDeep
     */
    function testGetDeep() {
        $arr = ['a' => 1, 2 => ['b' => 'c'], 'd' => ['e' => ['f' => [3, 4]]]];
        $this->assertSame('c', Arr::getDeep($arr, '2[b]'));
        $this->assertSame([3, 4], Arr::getDeep($arr, 'd[e][f]'));
        $this->assertSame(null, Arr::getDeep($arr, 'd[g][f]'));
        $this->assertSame('x', Arr::getDeep($arr, 'd[e][g]', 'x'));

        $arr2 = ['foos' => ['frank', 'furt'], 'bars' => ['big', 'baz']];
        $this->assertSame(['big','baz'], Arr::getDeep($arr2, 'bars', []));
        $this->assertSame(['big','baz'], Arr::getDeep($arr2, 'bars[]', []));
        $this->assertSame([], Arr::getDeep($arr2, 'beans[]', []));

        $arr3 = ['icanbe'=>[''=>'emptystr']];
        $this->assertSame('emptystr', Arr::getDeep($arr3, 'icanbe[]', ''));
    }
    /**
     * @param $arr
     * @param $quick
     * @param $is_assoc
     * @dataProvider arrayTypeArgs
     * @covers \Ptilz\Arr::isAssoc
     */
    public function testIsAssoc($arr, $quick, $is_assoc) {
        $this->assertSame($is_assoc, Arr::isAssoc($arr, $quick));
    }

    /**
     * @param $arr
     * @param $quick
     * @param $is_assoc
     * @dataProvider arrayTypeArgs
     * @covers \Ptilz\Arr::isNumeric
     */
    public function testIsNumeric($arr, $quick, $is_assoc) {
        $this->assertSame(!$is_assoc, Arr::isNumeric($arr, $quick));
    }

    public static function arrayTypeArgs() {
        return [
            [[1,2,3],false,false],
            [['a'=>1,'b'=>2,'c'=>3],false,true],
            [[1=>1,2=>2,3=>3],false,true],
            [[1,2,9=>3],false,true],
            [[],false,false],

            [[1,2,3],true,false],
            [['a'=>1,'b'=>2,'c'=>3],true,true],
            [[1=>1,2=>2,3=>3],true,true],
            [[1,2,9=>3],true,true],
            [[],true,false],
            [[1,2,3,4,5,6,7,8,9,0,'foo'=>'bar',1,2,3,4,5,6,7,8,9,0],true,true],
            [[0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19],true,false],
            [[0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19],false,false],
            [[0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,99=>99,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19],false,true],
            // [[0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,99=>99,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19],true,true], // this one tricks the 'quick' test
            [[0=>0,1=>1,99=>99,3=>3,4=>4,],false,true],
            [[0=>0,1=>1,99=>99,3=>3,4=>4,],true,true],
        ];
    }

    public static function isOdd($val) {
        return ($val & 1) === 1;
    }

    public static function isPrivate($_, $key) {
        return is_string($key) && strlen($key) >= 1 && $key[0] === '_';
    }

    /**
     * @covers \Ptilz\Arr::remove
     */
    function testRemove() {
        $this->assertSame([2, 4, 6], Arr::remove([1, 2, 3, 4, 5, 6], ['ArrTest', 'isOdd']));
        $this->assertSame(['b' => 2], Arr::remove(['a' => 1, 'b' => 2], ['ArrTest', 'isOdd']));
        $this->assertSame(['username' => 'mark'], Arr::remove(['username' => 'mark', '_password' => 'secret'], ['ArrTest', 'isPrivate']));
    }

    /**
     * @covers \Ptilz\Arr::filter
     */
    function testFilter() {
        $this->assertSame([1, 3, 5], Arr::filter([1, 2, 3, 4, 5, 6], ['ArrTest', 'isOdd']), "Filter by value");
        $this->assertSame(['a' => 1], Arr::filter(['a' => 1, 'b' => 2], ['ArrTest', 'isOdd']), "Filter, maintaining keys");
        $this->assertSame(['_foo' => 'bar'], Arr::filter(['_foo' => 'bar', 'baz'], ['ArrTest', 'isPrivate']), "Filter by key");
        $this->assertSame(['0', "\0", -1, 1, true, [0]], Arr::filter(['', '0', "\0", -1, 0, 1, true, false, null, [], [0]]), "Default filter");
    }

    /**
     * @covers \Ptilz\Arr::firstValue
     */
    function testFirstValue() {
        $arr = [1, 2, 3];
        $dict = ['a' => 1, 'b' => 2];
        $this->assertSame(1, Arr::firstValue($arr));
        $this->assertSame(1, Arr::firstValue($dict));
    }

    /**
     * @covers \Ptilz\Arr::firstKey
     */
    function testFirstKey() {
        $arr = [1, 2, 3];
        $dict = ['a' => 1, 'b' => 2];
        $this->assertSame(0, Arr::firstKey($arr));
        $this->assertSame('a', Arr::firstKey($dict));
    }

    /**
     * @covers \Ptilz\Arr::lastValue
     */
    function testLastValue() {
        $arr = [1, 2, 3];
        $dict = ['a' => 1, 'b' => 2];
        $this->assertSame(3, Arr::lastValue($arr));
        $this->assertSame(2, Arr::lastValue($dict));
    }

    /**
     * @covers \Ptilz\Arr::lastKey
     */
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

    /**
     * @covers \Ptilz\Arr::rekey
     */
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

    /**
     * @covers \Ptilz\Arr::pluck
     */
    function testPluck() {
        $this->assertSame(['Steve', 'Susan'], Arr::pluck(self::$people, 'name'));
        $this->assertSame(['a' => 'a2', 'b' => 'b2'],
            Arr::pluck([
                'a' => ['k1' => 'a1', 'k2' => 'a2'],
                'b' => ['k1' => 'b1', 'k2' => 'b2'],
            ], 'k2'));
    }

    /**
     * @covers \Ptilz\Arr::only
     */
    function testOnly() {
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

    /**
     * @covers \Ptilz\Arr::pop
     */
    function testPop() {
        $arr = [1,2,3,4,5,6];
        $this->assertSame(6,Arr::pop($arr));
        $this->assertSame([1,2,3,4,5],$arr);
        $this->assertSame(5,Arr::pop($arr,null,7));
        $this->assertSame([1,2,3,4],$arr);
        $this->assertSame(4,Arr::pop($arr,null,7));
        $this->assertSame(3,Arr::pop($arr,null,7));
        $this->assertSame(2,Arr::pop($arr,null,7));
        $this->assertSame(1,Arr::pop($arr,null,7));
        $this->assertSame([],$arr);
        $this->assertSame(7,Arr::pop($arr,null,7));
        $this->assertSame([],$arr);

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
        $this->assertSame(5,Arr::pop($arr,null,7));
        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ],$arr);
        $this->assertSame(7,Arr::pop($arr,'f',7));
        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ],$arr);
    }

    /**
     * @covers \Ptilz\Arr::keysUnion
     */
    function testKeysUnion() {
        $this->assertSame(['a', 'b', 'c'], Arr::keysUnion(['a' => 1, 'b' => 2], ['b' => 2, 'c' => 3]));
        $this->assertSame([0, 1, 2, 3], Arr::keysUnion([1, 2, 3], ['a', 'b', 'c'], ['W', 'X', 'Y', 'Z']));
    }

    /**
     * @covers \Ptilz\Arr::keysIntersection
     */
    function testKeysIntersection() {
        $this->assertSame(['b'], Arr::keysIntersection(['a' => 1, 'b' => 2], ['b' => 2, 'c' => 3]));
        $this->assertSame([0, 1, 2], Arr::keysIntersection([1, 2, 3], ['a', 'b', 'c'], ['W', 'X', 'Y', 'Z']));
    }

    /**
     * @covers \Ptilz\Arr::zip
     */
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

    /**
     * @covers \Ptilz\Arr::concat
     */
    function testConcat() {
        $this->assertSame([1, 2, 3, 3, 4, 5], Arr::concat(['a' => 1, 'b' => 2, 'c' => 3], [3, 4, 5]));
    }

    /**
     * @covers \Ptilz\Arr::merge
     */
    function testMerge() {
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3, 0 => 3, 1 => 4, 2 => 5], Arr::merge(['a' => 1, 'b' => 2, 'c' => 3], [3, 4, 5]));
        $this->assertSame([3, 4, 5, 'z'], Arr::merge([1, 2, 3, 'z'], [3, 4, 5]));
    }

    /**
     * @covers \Ptilz\Arr::mergeRecursive
     */
    function testMergeRecursive() {
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3, 0 => 3, 1 => 4, 2 => 5], Arr::mergeRecursive(['a' => 1, 'b' => 2, 'c' => 3], [3, 4, 5]));
        $this->assertSame([3, 4, 5, 'z'], Arr::mergeRecursive([1, 2, 3, 'z'], [3, 4, 5]));
        $this->assertSame(['a'=>[1,2,3,4],'b'=>'z','c'=>'y','d'=>'w'], Arr::mergeRecursive(['a'=>[1,2],'b'=>'x','c'=>'y'], ['a'=>[3,4],'b'=>'z','d'=>'w']));
        $this->assertSame(['a'=>[3,4,3=>5]], Arr::mergeRecursive(['a'=>[1,2]], ['a'=>[3,4,3=>5]]));
        $this->assertSame(['a'=>[1,2,3=>5]], Arr::mergeRecursive(['a'=>[3,4,3=>5]], ['a'=>[1,2]]));
        $this->assertSame(['a'=>['b'=>[1,2,3,4]]], Arr::mergeRecursive(['a'=>['b'=>[1,2]]], ['a'=>['b'=>[3,4]]]));
    }

    /**
     * @covers \Ptilz\Arr::extend
     */
    function testExtend() {
        $arr = [1, 2, 3];
        $this->assertSame([1, 2, 3, 3, 4, 5], Arr::extend($arr, [3, 4, 5]));
        $this->assertSame([1, 2, 3, 3, 4, 5], $arr);

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        Arr::extend($arr, ['c' => 4, 'd' => 5]);
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 4, 'd' => 5], $arr);
    }

    /**
     * @covers \Ptilz\Arr::regroup
     */
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

    /**
     * @covers \Ptilz\Arr::zipdict
     */
    function testZipdict() {
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], Arr::zipdict(['a', 'b', 'c'], [1, 2, 3]));
    }

    /**
     * @covers \Ptilz\Arr::flatten
     */
    function testFlatten() {
        $this->assertSame([1, 2, 3, 4, 5, 6], Arr::flatten([[1, [], 2, 3], [4, [5], 6]]));
    }

    /**
     * @covers \Ptilz\Arr::readable
     */
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

    /**
     * @covers \Ptilz\Arr::transpose
     */
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


        $this->assertSame(['a'=>[1,3],'b'=>[2,4]], Arr::transpose([['a' => 1,'b'=>2],['a'=>3,'b'=>4]]));
    }

    /**
     * @covers \Ptilz\Arr::map
     */
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

    /**
     * @covers \Ptilz\Arr::wrap
     */
    function testWrap() {
        $this->assertSame(['<b>', '<i>', '<u>'], Arr::wrap(['b', 'i', 'u'], '<', '>'));
        $this->assertSame('<b><i><u>', Arr::wrap(['b', 'i', 'u'], '<', '>', ''));
        $this->assertSame('[a],[b],[c]', Arr::wrap(['a', 'b', 'c'], '[', ']', ','));
    }

    /**
     * @covers \Ptilz\Arr::inc
     */
    function testInc() {
        $arr = ['a' => 1];
        Arr::inc($arr, 'a');
        $this->assertSame(['a' => 2], $arr);
        Arr::inc($arr, 'b', 2);
        $this->assertSame(['a' => 2, 'b' => 2], $arr);
        Arr::inc($arr, ['c','d'], 3);
        $this->assertSame(['a' => 2, 'b' => 2, 'c' => ['d' => 3]], $arr);
    }

    /**
     * @covers \Ptilz\Arr::dict
     */
    function testDict() {
        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3
        ], Arr::dict([
            ['a', 1],
            ['b', 2],
            ['c', 3],
        ]));

        $out = [
            '2015-01-01' => 1,
            '2015-01-02' => 2,
            '2015-01-03' => 3
        ];
        $in = [
            ['date' => '2015-01-01', 'count' => 1, 'type' => 'car'],
            ['date' => '2015-01-02', 'count' => 2, 'type' => 'car'],
            ['date' => '2015-01-03', 'count' => 3, 'type' => 'truck'],
        ];
        $this->assertSame($out, Arr::dict($in));
        $this->assertSame($out, Arr::dict($in, 'date', 'count'));
        $this->assertSame(['car'=>[1,2],'truck'=>3], Arr::dict($in, 'type', 'count'));
    }

    /**
     * @covers \Ptilz\Arr::push
     */
    function testPush() {
        $arr = ['a'=>[1,2]];
        Arr::push($arr,'a',3);
        $this->assertSame(['a'=>[1,2,3]],$arr);
        Arr::push($arr,['b','c'],4);
        $this->assertSame(['a'=>[1,2,3],'b'=>['c'=>[4]]],$arr);
        
        $arr2 = [1,2];
        Arr::push($arr2,3);
        $this->assertSame([1,2,3],$arr2);
    }

    /**
     * @covers \Ptilz\Arr::removeKeyPrefix
     */
    function testRemoveKeyPrefix() {
        $arr1 = [
            'foo_bar' => 1,
            'foo_baz' => 2,
            'foo_quux' => 3,
            'corge_corgi' => 4,
        ];
        
        $this->assertSame([
            'bar' => 1,
            'baz' => 2,
            'quux' => 3,
            'corge_corgi' => 4,
        ], Arr::removeKeyPrefix($arr1,'foo_'));

        $this->assertSame([
            'bar' => 1,
            'baz' => 2,
            'quux' => 3,
        ], Arr::removeKeyPrefix($arr1,'foo_',true));
    }

    /**
     * @covers \Ptilz\Arr::repeat
     */
    function testRepeat() {
        $this->assertSame([1,1,1], Arr::repeat(1,3));
        $this->assertSame([null,null], Arr::repeat(null,2));
    }
    
    /**
     * @covers \Ptilz\Arr::shuffle
     */
    function testShuffle() {
        $arr = range(0,9999);
        $shuffled1 = Arr::shuffle($arr);
        $this->assertCount(count($arr), $shuffled1);
        $this->assertSame(50, $shuffled1[50]);
        $shuffled2 = Arr::shuffle($arr,false);
        // $this->assertNotSame(50, $shuffled2[50]); // might fail by chance; not sure what to do about that
        $this->assertSame([], Arr::shuffle([]));
        $this->assertSame([5], Arr::shuffle([5]));
        asort($shuffled1);
        $this->assertSame($arr, $shuffled1);
        asort($shuffled2);
        $this->assertNotSame($arr, $shuffled2);
    }

    /**
     * @covers \Ptilz\Arr::randomKey
     */
    function testRandomKey() {
        $arr = range(5,9);
        for($i=0; $i<100; ++$i) {
            $key = Arr::randomKey($arr);
            $this->assertGreaterThanOrEqual(0, $key);
            $this->assertLessThan(5, $key);
        }
    }
    
    /**
     * @covers \Ptilz\Arr::randomValue
     */
    function testRandomValue() {
        $arr = range(5,9);
        for($i=0; $i<100; ++$i) {
            $val = Arr::randomValue($arr);
            $this->assertGreaterThanOrEqual(5, $val);
            $this->assertLessThan(10, $val);
        }
    }

    /**
     * @covers \Ptilz\Arr::randomSubset
     */
    function testRandomSubset() {
        $arr = range(5,9);
        $this->assertCount(3, Arr::randomSubset($arr,3));
        $this->assertCount(5, Arr::randomSubset($arr,10));
    }

    /**
     * @covers \Ptilz\Arr::toSentence
     */
    function testToSentence() {
        $this->assertSame('', Arr::toSentence([]));
        $this->assertSame('A', Arr::toSentence(['A']));
        $this->assertSame('A and B', Arr::toSentence(['A', 'B']));
        $this->assertSame('A, B and C', Arr::toSentence(['A', 'B', 'C']));
        $this->assertSame('A, B or C', Arr::toSentence(['A', 'B', 'C'], ', ', ' or '));
        $this->assertSame('A; B or C', Arr::toSentence(['A', 'B', 'C'], '; ', ' or '));
        $this->assertSame('A or B', Arr::toSentence(['A', 'B'], '; ', ' or ', true));
        $this->assertSame('A; B; or C', Arr::toSentence(['A', 'B', 'C'], '; ', ' or ', true));
    }

    /**
     * @covers \Ptilz\Arr::binarySearch
     */
    public function testBinarySearch() {
        $counting = [0, 1, 2, 3];
        $this->assertSame(2,Arr::binarySearch($counting,2));
        $this->assertSame(0,Arr::binarySearch($counting,0));
        $this->assertSame(3,Arr::binarySearch($counting,3));
        $this->assertSame(~0,Arr::binarySearch([],3));
        
        $even = [2, 4, 6];
        $this->assertSame(~1,Arr::binarySearch($even,3));
        $this->assertSame(~2,Arr::binarySearch([2,4,6],5));
        $this->assertSame(~3,Arr::binarySearch([2,4,6],7));
        $this->assertSame(~0,Arr::binarySearch([2,4,6],1));
        
        $alpha = ['alpha', 'BRAVO', 'Charle', 'delta'];
        $this->assertSame(1,Arr::binarySearch($alpha,'bravo','strcasecmp'));
        $this->assertSame(~2,Arr::binarySearch($alpha,'buzz','strcasecmp'));
    }
}
