<?php
use Ptilz\Bin;

require __DIR__.'/../vendor/autoload.php';

$zip = file_get_contents(__DIR__.'/unpack.zip');

//var_dump(unpack('a4',"PK"));

$offset = 0;

var_dump(Bin::unpack([
    'str[4]',
    '-uint16',
    '-uint16',
    '-uint16',
    '-uint16',
    '-uint16',
    '-uint32',
    '-uint32',
    '-uint32',
    '-uint16',
    '-uint16',
//    'str[$filename_len]',
//    'str[$extra_field_len]',
],$zip,$offset));

$pkZipFormat = [
    'signature' => 'str[4]',
    'version' => '-uint16',
    'flags' => '-uint16',
    'compression' => '-uint16',
    'modtime' => '-uint16',
    'moddate' => '-uint16',
    'crc32' => '-uint32',
    'compressed_size' => '-uint32',
    'uncompressed_size' => '-uint32',
    'filename_len' => '-uint16',
    'extra_field_len' => '-uint16',
    'filename' => 'str[$filename_len]',
    'extra_field' => 'str[$extra_field_len]',
    'compressed_data' => 'str[$compressed_size]',
];

var_dump(Bin::unpack($pkZipFormat,$zip));