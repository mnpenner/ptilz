<?php
namespace Ptilz;

use Ptilz\Exceptions\ArgumentException;
use Ptilz\Exceptions\NotImplementedException;

/**
 * Functions for working with binary data
 */
abstract class Bin {

    public static function unpack(array $formatArray, $data, &$offset=0) {
        $packArgs = [];
        if($offset!==0) $packArgs[] = "@$offset";
        $result = [];
        $sizeOfInt = strlen(decbin(~0))/8; // not sure if there's a better way to find this out

        $patt = '~
            (?|
                (?<type>
                      char
                    | byte
                    | int
                    | uint
                    | [-+]?int16
                    | [-+]?uint16
                    | [-+]?int32
                    | [-+]?uint32
                    | [-+]?int64
                    | [-+]?uint64
                    | float
                    | double
                )
                | (?<type>str) (?:\[ (?<len>\$\w+|\d+) \])
            )\z
            ~Amsx';

        foreach($formatArray as $key => $type) {
            if(!preg_match($patt, $type, $m)) {
                throw new ArgumentException('format',"`$key` has an unrecognized value of '$type'");
            }

            switch($m['type']) {
                case 'char':
                    $formatStr = 'c';
                    ++$offset;
                    break;
                case 'byte':
                    $formatStr = 'C';
                    ++$offset;
                    break;
                case 'int':
                    $formatStr = 'i';
                    $offset += $sizeOfInt;
                    break;
                case 'uint':
                    $formatStr = 'I';
                    $offset += $sizeOfInt;
                    break;
                case 'int16':
                    $formatStr = 's';
                    $offset += 2;
                    break;
                case 'uint16':
                    $formatStr = 'S';
                    $offset += 2;
                    break;
                case '+uint16':
                    $formatStr = 'n';
                    $offset += 2;
                    break;
                case '-uint16':
                    $formatStr = 'v';
                    $offset += 2;
                    break;
                case 'int32':
                    $formatStr = 'l';
                    $offset += 4;
                    break;
                case 'uint32':
                    $formatStr = 'L';
                    $offset += 4;
                    break;
                case '+uint32':
                    $formatStr = 'N';
                    $offset += 4;
                    break;
                case '-uint32':
                    $formatStr = 'V';
                    $offset += 4;
                    break;
                case 'float':
                    $formatStr = 'f';
                    $offset += $sizeOfInt;
                    break;
                case 'double':
                    $formatStr = 'd';
                    $offset += 2*$sizeOfInt;
                    break;
                case 'str':
                    if($m['len'][0] === '$') {
                        $packFormatStr = implode('/',$packArgs);
                        Arr::extend($result, unpack($packFormatStr,$data));
                        $packArgs = ["@$offset"];
                        $lenKey = substr($m['len'],1);
                        if(!array_key_exists($lenKey,$result)) {
                            throw new ArgumentException("Length value $m[len] has not been read in (yet) for `$key`");
                        }
                        $strlen = $result[$lenKey];
                    } else {
                        $strlen = (int)$m['len'];
                    }
                    $formatStr = 'a' . $strlen;
                    $offset += $strlen;
                    break;
                default:
                    throw new NotImplementedException("Type '$m[type]' has not been implemented yet");
            }

            if(is_string($key)) {
                $formatStr .= $key;
            }

            $packArgs[] = $formatStr;
        }

        $packFormatStr = implode('/',$packArgs);
        Arr::extend($result, unpack($packFormatStr,$data));

//        echo "PACK FORMAT: $packFormatStr\n";
        return $result;
    }
}