<?php
namespace QueryBuilder;

class Value extends Expr {
    protected $val;

    public function __construct($value) {
        $this->val = $value;
    }
}