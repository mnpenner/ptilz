<?php
namespace Ptilz;

use DateTime;
use mysqli;
use PDO;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Exceptions\UnreachableException;
use Ptilz\Internal\RawSql;

// FIXME: make this class non-static; it won't work well with multiple connections if the $connection is static!
// this might actually make more sense if we make it abstract (or an interface) and then extend it for Pdo, mysql, mysqli etc with a generic quote() function!

/**
 * @deprecated Use PdoPlus
 * @see https://bitbucket.org/nucleuslabs/pdoplus
 */
abstract class Sql {
    /** @var PDO|mysqli|resource Connection used for escaping values  */
    public static $connection = null;

    /**
     * Escapes special characters for use in a LIKE clause.
     * @param string $str
     * @return string
     */
    public static function escapeLike($str) {
        return str_replace(['%', '_'], ['\\%', '\\_'], $str);
    }

    /**
     * @param mixed $value
     * @throws Exceptions\ArgumentTypeException
     * @return string
     */
    public static function quote($value) {
        if(is_null($value)) return 'NULL';
        elseif(is_bool($value)) return $value ? '1' : '0';
        elseif(is_int($value) || is_float($value) || $value instanceof RawSql) return (string)$value;
        elseif($value instanceof DateTime) return "'" . $value->format('Y-m-d H:i:s') . "'";
        elseif(is_array($value)) {
            if(!$value) return '/* empty array */';
            if(Arr::isAssoc($value)) {
                $pairs = [];
                foreach($value as $k => $v) {
                    $pairs[] = self::escapeId($k) . '=' . self::quote($v);
                }
                return implode(', ', $pairs);
            }
            return '(' . implode(',', array_map(__METHOD__, $value)) . ')';
        } elseif(is_string($value)) {
            if(self::$connection instanceof PDO) return self::$connection->quote($value, PDO::PARAM_STR);
            if(self::$connection instanceof mysqli) self::$connection->real_escape_string($value);
            if(self::isMySqlLink(self::$connection)) return "'" . mysql_real_escape_string($value, self::$connection) . "'";
            if(self::$connection === null) return "'" . str_replace(["'", '\\', "\0", "\t", "\n", "\r", "\x08", "\x1a"], ["''", '\\\\', '\\0', '\\t', '\\n', '\\r', '\\b', '\\Z'], $value) . "'"; // WARNING: this is not safe if NO_BACKSLASH_ESCAPES is enabled or if the server character set is one of big5, cp932, gb2312, bgk or sjis; see http://stackoverflow.com/a/12118602/65387 for details
            throw new ArgumentTypeException('conn', 'PDO|mysqli');
        }
        throw new ArgumentTypeException('value', 'string');
    }

    /**
     * Tests if an object is a "mysql link".
     *
     * @param mixed $resource
     * @return bool
     */
    public static function isMySqlLink($resource) {
        return is_resource($resource) && get_resource_type($resource) === 'mysql link';
    }

    /**
     * Returns a "literal" or "raw" value which will not be escaped by Sql::quote
     *
     * @param string $str Raw value (should be valid SQL)
     * @return RawSql
     */
    public static function raw($str) {
        return new RawSql($str);
    }

    public static function bin($str) {
        return Str::isBlank($str) ? $str : new RawSql('0x' . bin2hex($str));
    }

    /**
     * Escape an identifier.
     *
     * @param string $id Identifier such as column or table name.
     * @param bool $forbidQualified If true, identifiers containing dots will be treated as a single unqualified identifier (e.g. `table.column`). If false, the $id string will be split into a qualified identifier (e.g. `table`.`column`).
     * @return mixed|string
     */
    public static function escapeId($id, $forbidQualified = false) {
        if($id instanceof RawSql) return (string)$id;
        if(is_array($id)) {
            return implode(',', array_map(function ($x) use ($forbidQualified) {
                return Sql::escapeId($x, $forbidQualified);
            }, $id));
        }
        $ret = '`' . str_replace('`', '``', $id) . '`';
        return $forbidQualified ? $ret : str_replace('.', '`.`', $ret);
    }

    /**
     * Formats a parameterized query by replacing ?, ??, :value, and ::table with corresponding parameters.
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public static function format($query, $params = []) {
        return preg_replace_callback('~(?|`(?:[^`\\\\]|\\\\.|``)*`|\'(?:[^\'\\\\]|\\\\.|\'\')*\'|"(?:[^"\\\\]|\\\\.|"")*"|(\?{1,2})|(:{1,2})([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*))~', function ($matches) use (&$params) {
            if(!isset($matches[1])) return $matches[0];
            switch($matches[1]) {
                case '?':
                    if(!$params) throw new \DomainException("Not enough params");
                    return Sql::quote(array_shift($params));
                case '??':
                    if(!$params) throw new \DomainException("Not enough params");
                    return Sql::escapeId(array_shift($params));
                case ':':
                    if(!array_key_exists($matches[2],$params)) throw new \DomainException("\"$matches[2]\" param not provided");
                    return Sql::quote($params[$matches[2]]);
                case '::':
                    if(!array_key_exists($matches[2],$params)) throw new \DomainException("\"$matches[2]\" param not provided");
                    return Sql::escapeId($params[$matches[2]]);
            }
            throw new UnreachableException("Bad regex");
        }, $query);
    }

    public static function datetime($timestamp = null) {
        if($timestamp === null) $timestamp = time();
        elseif(is_string($timestamp) && !is_numeric($timestamp)) $timestamp = strtotime($timestamp);
        return date('Y-m-d H:i:s', $timestamp);
    }

    public static function date($timestamp = null) {
        if($timestamp === null) $timestamp = time();
        elseif(is_string($timestamp) && !is_numeric($timestamp)) $timestamp = strtotime($timestamp);
        return date('Y-m-d', $timestamp);
    }
}