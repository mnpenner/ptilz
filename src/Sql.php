<?php
namespace Ptilz;

use DateTime;
use Exception;
use PDO;
use Ptilz\Exceptions\UnreachableException;

class Sql {

    public static function escapeLike($str) {
        return str_replace(['%', '_'], ['\\%', '\\_'], $str);
    }

    public static function quote($value, PDO $conn = null) {
        if(is_null($value)) return 'NULL';
        elseif(is_bool($value)) return $value ? '1' : '0';
        elseif(is_int($value) || is_float($value) || $value instanceof _RawSql) return (string)$value;
        elseif($value instanceof DateTime) return "'" . $value->format('Y-m-d H:i:s') . "'";
        elseif(is_array($value)) {
            if(Arr::isAssoc($value)) {
                $pairs = [];
                foreach($value as $k => $v) {
                    $pairs[] = self::escapeId($k) . '=' . self::quote($v);
                }
                return implode(', ', $pairs);
            }
            return '(' . implode(', ', array_map(__METHOD__, $value)) . ')';
        }
        return $conn ? $conn->quote($value) : "'" . str_replace(["'", '\\', "\0", "\t", "\n", "\r", "\x08", "\x1a"], ["''", '\\\\', '\\0', '\\t', '\\n', '\\r', '\\b', '\\Z'], $value) . "'";
    }

    /**
     * Returns a "literal" or "raw" value which will not be escaped by Sql::quote
     *
     * @param string $str Raw value (should be valid SQL)
     * @return _RawSql
     */
    public static function raw($str) {
        return new _RawSql($str);
    }

    public static function bin($str) {
        return $str === null || $str === '' ? $str : new _RawSql('0x'.bin2hex($str));
    }

    public static function escapeId($id, $forbidQualified = false) {
        if($id instanceof _RawSql) return (string)$id;
        if(is_array($id)) {
            return implode(',', array_map(function ($x) use ($forbidQualified) {
                return Sql::escapeId($x, $forbidQualified);
            }, $id));
        }
        $ret = '`' . str_replace('`', '``', $id) . '`';
        return $forbidQualified ? $ret : str_replace('.', '`.`', $ret);
    }

    public static function format($query, $params = []) {
        return preg_replace_callback('~(?|(\?{1,2})|(:{1,2})([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*))~', function ($matches) use (&$params) {
            switch($matches[1]) {
                case '?':
                    return Sql::quote(array_shift($params));
                case '??':
                    return Sql::escapeId(array_shift($params));
                case ':':
                    return Sql::quote($params[$matches[2]]);
                case '::':
                    return Sql::escapeId($params[$matches[2]]);
            }
            throw new UnreachableException("Bad regex");
        }, $query);
    }
}