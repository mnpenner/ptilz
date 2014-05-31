<?php
namespace QueryBuilder;

function _or() {

}


/**
 * Equal operator, =
 */
function eq() {

}

/**
 * LIKE expr%
 */
function startsWith($value) {

}

/**
 * LIKE %expr
 */
function endsWith($value) {

}

function regexp($column,$pattern) {

}

function not($expr) {

}
function not_regexp($column,$pattern) {
    return 'NOT '.regexp($column,$pattern);
}


/**
 *
 */
function gt($column,$value) {

}

function lt($column,$value) {

}

function gte($column,$value) {

}

function lte($column,$value) {}

/**
 * $value BETWEEN a AND b
 */
function between($column,$min,$max){}

/**
 * expr IS NULL
 */
function is_null($column){}

/**
 * ISNULL(expr)
 */
function isnull($column){}

/**
 * LIKE %expr%
 */
function contains($column,$value) {}

/**
 * expr LIKE pat [ESCAPE 'escape_char']
 */
function like($column,$patter,$escape_char=null) {

}

/**
 * NULL-safe equal to operator, <=>
 */
function nse() {

}

/**
 * Do not escape
 * @param $expr
 */
function raw($expr) {}

/**
 * Escape as though this is a value
 * @param $val
 */
function val($val) {}

/**
 * Escape as though this is a column
 * @param $col
 */
function col($col) {}