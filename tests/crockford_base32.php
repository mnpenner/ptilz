<?php
/*
Some things to consider:

> OpenSSL was the culprit. More specifically, the use of openssl_random_pseudo_bytes() when using PHP in forked child processes, as is the case when using PHP with Apache or PHP-FPM. The processes were wrapping, so the children would produce the same random sequences as previous children with the same process IDs.

https://benramsey.com/blog/2016/04/ramsey-uuid/
https://github.com/ramsey/uuid/issues/117
https://github.com/ramsey/uuid/issues/90
https://github.com/paragonie/random_compat
https://paragonie.com/blog/2015/07/how-safely-generate-random-strings-and-integers-in-php
https://github.com/ircmaxell/RandomLib
 */


// https://en.wikipedia.org/wiki/Base32#Crockford.27s_Base32
use Ptilz\Bin;

require __DIR__ . '/../vendor/autoload.php';


//function crockford32_encode($data) {
//    $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
//    $blocks = str_split($data,5); // 40-bit blocks
//    foreach($blocks as $block) {
//        if(strlen($block) < 5) {
//            $block = str_pad($block, 5, "\x00");
//        }
//        $b = array_map('ord', $block);
//        $chars = [
//            $b[0] & 0b00011111,
//            (($b[0] & 0b11100000) << 3) | ($b[1] & 0b00000011),
//            $b[1] & 0b01111100,
//        ];
//
//    }
//}

function crockford32_encode($data) {
    return implode('', array_map(function ($d) {
        return '0123456789abcdefghjkmnpqrstvwxyz'[$d];
    }, array_map('bindec', str_split(str_pad(implode('', array_map(function ($s) {
        return str_pad($s, 8, '0', STR_PAD_LEFT);
    }, array_map('decbin', array_map('ord', str_split($data))))),ceil(strlen($data)*8/5)*5,'0',STR_PAD_RIGHT), 5))));
}

function crockford32_decode($data) {
    $map = [
        '0' => 0, 'O' => 0, 'o' => 0,
        '1' => 1, 'I' => 1, 'i' => 1, 'L' => 1, 'l' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'A' => 10, 'a' => 10,
        'B' => 11, 'b' => 11,
        'C' => 12, 'c' => 12,
        'D' => 13, 'd' => 13,
        'E' => 14, 'e' => 14,
        'F' => 15, 'f' => 15,
        'G' => 16, 'g' => 16,
        'H' => 17, 'h' => 17,
        'J' => 18, 'j' => 18,
        'K' => 19, 'k' => 19,
        'M' => 20, 'm' => 20,
        'N' => 21, 'n' => 21,
        'P' => 22, 'p' => 22,
        'Q' => 23, 'q' => 23,
        'R' => 24, 'r' => 24,
        'S' => 25, 's' => 25,
        'T' => 26, 't' => 26,
        'V' => 27, 'v' => 27,
        'W' => 28, 'w' => 28,
        'X' => 29, 'x' => 29,
        'Y' => 30, 'y' => 30,
        'Z' => 31, 'z' => 31,
    ];

    $buf = array_fill(0, count($data), 0);
    $len = strlen($data);
    for($i=0; $i<$len; ++$i) {
        $c = $data[$i];
        if(!isset($map[$c])) {
            throw new \Exception("Unsupported character '$c' (0x".bin2hex($c).") at position $i");
        }
        $buf[$i] = $map[$c];
    }

    //dump($buf);
    //dump(str_split(substr(implode('',array_map(function($x) { return str_pad($x,5,'0',STR_PAD_LEFT); }, array_map('decbin', $buf))),0,floor($len*5/8)*8),8));
    //dump(implode('',array_map(function($x) { return str_pad($x,5,'0',STR_PAD_LEFT); }, array_map('decbin', $buf))));
    //dump(str_split(str_pad(implode('',array_map(function($x) { return str_pad($x,5,'0',STR_PAD_LEFT); }, array_map('decbin', $buf))),ceil($len*5/8)*8,'0',STR_PAD_RIGHT),8));

    return implode('',array_map('chr',array_map('bindec',str_split(substr(implode('',array_map(function($x) { return str_pad($x,5,'0',STR_PAD_LEFT); }, array_map('decbin', $buf))),0,floor($len*5/8)*8),8))));
}

/**
 * Generate a 125-bit (25 alphanumeric ASCII chars) universally unique identifier. Encode using Crockford's base-32 encoding algorithm
 * to make it:
 *
 * - Human readable (remove look-alike characters)
 * - More compact than base 16
 * - Only 3 more bytes more than base64
 * - Case-insensitive
 *
 * @return string
 * @see http://www.crockford.com/wrmg/base32.html
 */
function uuid() {
    return implode('', array_map(function ($d) {
        return '0123456789abcdefghjkmnpqrstvwxyz'[$d];
    },array_map('bindec',array_slice(str_split(implode('', array_map(function ($s) {
        return str_pad($s, 8, '0', STR_PAD_LEFT);
    }, array_map('decbin', array_map('ord', str_split(openssl_random_pseudo_bytes(16)))))),5),0,-1))));
}

//for($i=0; $i<100000; ++$i) {
//    $bytes = openssl_random_pseudo_bytes(mt_rand(1,100));
//    if(crockford32_decode(crockford32_encode($bytes)) !== $bytes) {
//        dump($bytes);
//    }
//}

//for($i=0; $i<1; ++$i) {
//    //$bytes = Bin::secureRandomBytes(mt_rand(1, 100));
//    //if(crockford32_decode(crockford32_encode($bytes)) !== $bytes) {
//    //    dump($bytes);
//    //    exit;
//    //}
////    dump(crockford32_encode($bytes));
////    dump(uuid());
//    $uuid = uuid();
//    $raw = crockford32_decode($uuid);
////    dump(strlen($uuid));
////    dump(strlen($raw)); // wtf?? where'd the last byte go?
////    $uuid = openssl_random_pseudo_bytes(16);
//    dump('uuid   '.$uuid);
//    dump('base32 '.crockford32_encode($raw));
//    dump('base64 '.base64_encode($raw));
//    dump('base16 '.bin2hex($raw));
//}

for($i=0; $i<10; ++$i) {
    dump(uuid());
}

//echo implode(' ',array_map(function($x) { return str_pad($x,5,'0',STR_PAD_RIGHT); },str_split(implode('', array_map(function ($s) {
//        return str_pad($s, 8, '0', STR_PAD_LEFT);
//    }, array_map('decbin', array_map('ord', str_split('f0oBaR'))))),5))).PHP_EOL;
//
//
//echo implode(', ',array_map('bindec',array_map(function($x) { return str_pad($x,5,'0',STR_PAD_RIGHT); },str_split(implode('', array_map(function ($s) {
//    return str_pad($s, 8, '0', STR_PAD_LEFT);
//}, array_map('decbin', array_map('ord', str_split('f0oBaR'))))),5))));

//dump(crockford32_encode('Mark'));
//dump(crockford32_decode('9ngq4tr'));

function f2($b, $n) {
    return array_map('bindec', array_map(function ($x) use ($n) {
        return str_pad($x, $n, '0', STR_PAD_RIGHT);
    }, str_split(implode('', array_map(function ($s) {
        return str_pad($s, 8, '0', STR_PAD_LEFT);
    }, array_map('decbin', array_map('ord', str_split($b))))), 5)));
}


//function f($b,$n){return array_map('bindec',array_map(function($x)use($n){return str_pad($x,$n,'0');},str_split(implode('',array_map(function($s){return str_pad($s,8,'0',STR_PAD_LEFT);},array_map('decbin',$b))),5)));}
function f($b, $n) {
    return array_map('bindec', array_map(function ($x) use ($n) {
        return str_pad($x, $n, '0');
    }, str_split(implode('', array_map(function ($s) {
        return str_pad($s, 8, '0', STR_PAD_LEFT);
    }, array_map('decbin', $b))), 5)));
}


//echo implode(',',array_map('ord', str_split('f0oBaR')));
dump(f2('f0oBaR',5));
dump(f([102,48,111,66,97,82],5));


//dump(crockford32_encode("A"));
//dump(crockford32_decode("A"));
//dump(crockford32_decode("AA"));
//dump(crockford32_decode("AAA"));
//dump(crockford32_decode("AAAA"));
//dump(crockford32_decode("AAAAA"));
//dump(crockford32_decode("AAAAAA"));

__halt_compiler();

"uuid   x6xc2d4xh5fwczt94e4xz90vx0"
"uuid   e195kkce43qx94dz9wc8ntmge"
"base32 e195kkce43qx94dz9wc8ntmg"
