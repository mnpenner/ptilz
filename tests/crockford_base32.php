<?php

// https://en.wikipedia.org/wiki/Base32#Crockford.27s_Base32
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
    $chars = '0123456789abcdefghjkmnpqrstvwxyz';
    $mask = 0b11111;

    $dataSize = strlen($data);
    $res = '';
    $remainder = 0;
    $remainderSize = 0;

    for($i = 0; $i < $dataSize; $i++) {
        $b = ord($data[$i]);
        $remainder = ($remainder << 8) | $b;
        $remainderSize += 8;
        while($remainderSize > 4) {
            $remainderSize -= 5;
            $c = $remainder & ($mask << $remainderSize);
            $c >>= $remainderSize;
            $res .= $chars[$c];
        }
    }
    if($remainderSize > 0) {
        $remainder <<= (5 - $remainderSize);
        $c = $remainder & $mask;
        $res .= $chars[$c];
    }

    return $res;
}

function crockford32_decode($data) {
    $map = [
        '0' => 0,
        'O' => 0,
        'o' => 0,
        '1' => 1,
        'I' => 1,
        'i' => 1,
        'L' => 1,
        'l' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'A' => 10,
        'a' => 10,
        'B' => 11,
        'b' => 11,
        'C' => 12,
        'c' => 12,
        'D' => 13,
        'd' => 13,
        'E' => 14,
        'e' => 14,
        'F' => 15,
        'f' => 15,
        'G' => 16,
        'g' => 16,
        'H' => 17,
        'h' => 17,
        'J' => 18,
        'j' => 18,
        'K' => 19,
        'k' => 19,
        'M' => 20,
        'm' => 20,
        'N' => 21,
        'n' => 21,
        'P' => 22,
        'p' => 22,
        'Q' => 23,
        'q' => 23,
        'R' => 24,
        'r' => 24,
        'S' => 25,
        's' => 25,
        'T' => 26,
        't' => 26,
        'V' => 27,
        'v' => 27,
        'W' => 28,
        'w' => 28,
        'X' => 29,
        'x' => 29,
        'Y' => 30,
        'y' => 30,
        'Z' => 31,
        'z' => 31,
    ];

    $data = strtolower($data);
    $dataSize = strlen($data);
    $buf = 0;
    $bufSize = 0;
    $res = '';

    for($i = 0; $i < $dataSize; $i++) {
        $c = $data[$i];
        if(!isset($map[$c])) {
            throw new \Exception("Unsupported character $c (0x".bin2hex($c).") at position $i");
        }
        $b = $map[$c];
        $buf = ($buf << 5) | $b;
        $bufSize += 5;
        if($bufSize > 7) {
            $bufSize -= 8;
            $b = ($buf & (0xff << $bufSize)) >> $bufSize;
            $res .= chr($b);
        }
    }

    return $res;
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
 * @param bool $raw_output
 * @return string
 * @see http://www.crockford.com/wrmg/base32.html
 */
function uuid($raw_output=false) {
    $bytes = openssl_random_pseudo_bytes(16);
    $bytes[15] = chr(ord($bytes) & 0b11111000); // chop off 3 bits to make 125 bits total
    return $raw_output ? $bytes : substr(crockford32_encode($bytes),0,-1);
}

//for($i=0; $i<100000; ++$i) {
//    $bytes = openssl_random_pseudo_bytes(mt_rand(1,100));
//    if(crockford32_decode(crockford32_encode($bytes)) !== $bytes) {
//        dump($bytes);
//    }
//}

for($i=0; $i<10; ++$i) {
    $uuid = uuid();
    $raw = crockford32_decode($uuid);
    dump(strlen($raw)); // fixme: wtf?? where'd the last byte go?
//    $uuid = openssl_random_pseudo_bytes(16);
    dump('base64 '.base64_encode($raw));
    dump('base32 '.$uuid);
    dump('base16 '.bin2hex($raw));
}

