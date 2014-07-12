<?php
use Ptilz\Bin;

require __DIR__.'/../vendor/autoload.php';

$zip = file_get_contents('unpack.zip');

//var_dump(unpack('a4',"PK"));

var_dump(Bin::unpack([
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
],$zip));