<?php
namespace QueryBuilder;

class RawSql extends Expr {
    protected $val;

    public function __construct($value) {
        $this->val = $value;
    }

    public function toSql() {
        return $this->val;
    }
}