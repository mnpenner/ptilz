<?php
namespace QueryBuilder;

/**
 * Static helper functions for working with the Query Builder
 */
class QB {
    /**
     * @param string $id
     * @param bool   $forbidQualified
     * @return Identifier
     */
    public static function id($id, $forbidQualified = false) {
        return $id instanceof Identifier ? $id : new Identifier($id, $forbidQualified);
    }

    public static function val($val) {
        return $val instanceof Value ? $val : new Value($val);
    }

    /**
     * @param string|null $type
     * @return SubQuery
     */
    public static function subQuery($type = null) {
        return new SubQuery($type);
    }

    /**
     * @return SubQuery
     */
    public static function exists() {
        return call_user_func_array([new SubQuery('EXISTS'), 'from'], func_get_args());
    }

    /**
     * @return SubQuery
     */
    public static function any() {
        return call_user_func_array([new SubQuery('ANY'), 'from'], func_get_args());
    }

    /**
     * @return SubQuery
     */
    public static function all() {
        return call_user_func_array([new SubQuery('ALL'), 'from'], func_get_args());
    }

    /**
     * @param string $table
     * @return SelectQuery
     */
    public static function from($table) {
        return call_user_func_array([new SelectQuery, 'from'], func_get_args());
    }
}