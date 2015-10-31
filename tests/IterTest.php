<?php
use Ptilz\Iter;

class IterTest extends PHPUnit_Framework_TestCase {
    function testToArray() {
        $generator = function () {
            yield 1;
            yield 'b' => 2;
        };
        $this->assertSame([1, 'b' => 2], Iter::toArray($generator()));

        $array = ['recipe' => 'pancakes', 'egg', 'milk', 'flour'];
        $this->assertSame($array, Iter::toArray($array));
        $iterator = new ArrayIterator($array);
        $this->assertSame($array, Iter::toArray($iterator));
        $this->assertSame(array_values($array), Iter::toArray($iterator, false));
    }

    function testMap() {
        $timesTwo = function ($x) {
            return $x * 2;
        };
        $generator = function () {
            yield 1;
            yield 2;
            yield 3;
        };
        $result = Iter::map($generator(), $timesTwo);
        $this->assertInstanceOf('Generator', $result);
        $this->assertSame([2, 4, 6], Iter::toArray($result));
    }

    function testAll() {
        $this->assertTrue(Iter::all([1, 2, 'a', '0', 'b', ['c']]));
        $this->assertFalse(Iter::all([1, 2, 'a', 0, 'b', ['c']]));
        $this->assertTrue(Iter::all([1, '2', 3.14, '4e5', '0xDEADBEEF'], 'is_numeric'));
    }

    function testAny() {
        $this->assertTrue(Iter::any([0, false, true, null]));
        $this->assertFalse(Iter::any([0, false, []]));
        $this->assertFalse(Iter::any(['what', 'does', 'the', 'fox', 'say'], 'is_numeric'));
        $this->assertTrue(Iter::any(['yip', 'yiiiip', '0xcafe'], 'is_numeric'));
    }

    function testCountable() {
        $this->assertTrue(Iter::isCountable([]));
        $this->assertTrue(Iter::isCountable(new _Countable));
        $this->assertFalse(Iter::isCountable(5));
        $this->assertFalse(Iter::isCountable('foo'));
    }

    function testTake() {
        $iter = new ArrayIterator(range(1, 10));
        $this->assertSame([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5], iterator_to_array(Iter::take($iter, 5)));
    }

    /**
     * @depends testTake
     */
    function testSkip() {
        $iter = new ArrayIterator(range(1, 10));
        $this->assertSame([2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7], iterator_to_array(Iter::take(Iter::skip($iter, 2), 5)));
    }

    function testFibonacci() {
        $fib = [0,
            1,
            1,
            2,
            3,
            5,
            8,
            13,
            21,
            34,
            55,
            89,
            144,
            233,
            377,
            610,
            987,
            1597,
            2584,
            4181,
            6765,
            10946,
            17711,
            28657,
            46368,
            75025,
            121393,
            196418,
            317811,
            514229,
            832040,
            1346269,
            2178309,
            3524578,
            5702887,
            9227465,
            14930352,
            24157817,
            39088169,
            63245986,
            102334155,
            165580141,
            267914296,
            433494437,
            701408733,
            1134903170,
            1836311903,
            2971215073,
            4807526976,
            7778742049,
            12586269025,
            20365011074,
            32951280099,
            53316291173,
            86267571272,
            139583862445,
            225851433717,
            365435296162,
            591286729879,
            956722026041,
            1548008755920,
            2504730781961,
            4052739537881,
            6557470319842,
            10610209857723,
            17167680177565,
            27777890035288,
            44945570212853,
            72723460248141,
            117669030460994,
            190392490709135,
            308061521170129,
            498454011879264,
            806515533049393,
            1304969544928657,
            2111485077978050,
            3416454622906707,
            5527939700884757,
            8944394323791464,
            14472334024676221,
            23416728348467685,
            37889062373143906,
            61305790721611591,
            99194853094755497,
            160500643816367088,
            259695496911122585,
            420196140727489673,
            679891637638612258,
            1100087778366101931,
            1779979416004714189,
            2880067194370816120,
            4660046610375530309,
            7540113804746346429,
            12200160415121876738,
            19740274219868223167,
            31940434634990099905,
            51680708854858323072,
            83621143489848422977,
            135301852344706746049,
            218922995834555169026,
            354224848179261915075,
            573147844013817084101,
            927372692193078999176,
            1500520536206896083277,
            2427893228399975082453,
            3928413764606871165730,
            6356306993006846248183,
            10284720757613717413913,
            16641027750620563662096,
            26925748508234281076009,
            43566776258854844738105,
            70492524767089125814114,
            114059301025943970552219,
            184551825793033096366333,
            298611126818977066918552,
            483162952612010163284885,
            781774079430987230203437,
            1264937032042997393488322,
            2046711111473984623691759,
            3311648143516982017180081,
            5358359254990966640871840,
            8670007398507948658051921,
            14028366653498915298923761,
            22698374052006863956975682,
            36726740705505779255899443,
            59425114757512643212875125,
            96151855463018422468774568,
            155576970220531065681649693,
            251728825683549488150424261,
            407305795904080553832073954,
            659034621587630041982498215,
            1066340417491710595814572169,
            1725375039079340637797070384,
            2791715456571051233611642553,
            4517090495650391871408712937,
            7308805952221443105020355490,
            11825896447871834976429068427,
            19134702400093278081449423917,
            30960598847965113057878492344,
            50095301248058391139327916261,
            81055900096023504197206408605,
            131151201344081895336534324866,
            212207101440105399533740733471,
            343358302784187294870275058337,
            555565404224292694404015791808,
            898923707008479989274290850145,
            1454489111232772683678306641953,
            2353412818241252672952597492098,
            3807901929474025356630904134051,
            6161314747715278029583501626149,
            9969216677189303386214405760200,
            16130531424904581415797907386349,
            26099748102093884802012313146549,
            42230279526998466217810220532898,
            68330027629092351019822533679447,
            110560307156090817237632754212345,
            178890334785183168257455287891792,
            289450641941273985495088042104137,
            468340976726457153752543329995929,
            757791618667731139247631372100066,
            1226132595394188293000174702095995,
            1983924214061919432247806074196061,
            3210056809456107725247980776292056,
            5193981023518027157495786850488117,
            8404037832974134882743767626780173,
            13598018856492162040239554477268290,
            22002056689466296922983322104048463,
            35600075545958458963222876581316753,
            57602132235424755886206198685365216,
            93202207781383214849429075266681969,
            150804340016807970735635273952047185,
            244006547798191185585064349218729154,
            394810887814999156320699623170776339,
            638817435613190341905763972389505493,
            1033628323428189498226463595560281832,
            1672445759041379840132227567949787325,
            2706074082469569338358691163510069157,
            4378519841510949178490918731459856482,
            7084593923980518516849609894969925639,
            11463113765491467695340528626429782121,
            18547707689471986212190138521399707760,
            30010821454963453907530667147829489881,
            48558529144435440119720805669229197641,
            78569350599398894027251472817058687522,
            127127879743834334146972278486287885163,
            205697230343233228174223751303346572685,
            332825110087067562321196029789634457848,
            538522340430300790495419781092981030533,
            871347450517368352816615810882615488381,
            1409869790947669143312035591975596518914,
            2281217241465037496128651402858212007295,
            3691087032412706639440686994833808526209,
            5972304273877744135569338397692020533504,
            9663391306290450775010025392525829059713,
            15635695580168194910579363790217849593217,
            25299086886458645685589389182743678652930,
            40934782466626840596168752972961528246147,
            66233869353085486281758142155705206899077,
            107168651819712326877926895128666735145224,
            173402521172797813159685037284371942044301,
            280571172992510140037611932413038677189525,];

        $gen = Iter::fibonacci();
        for($i = 0; $i < count($fib) && $fib[$i] <= PHP_INT_MAX; ++$i, $gen->next()) {
            $this->assertSame($fib[$i], $gen->current());
        }
    }
}

class _Countable implements Countable {
    public function count() {
        return 1;
    }
}