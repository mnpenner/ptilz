<?php
namespace QueryBuilder;

use Ptilz\Sql;

class Value extends Expr {
    protected $val;

    public function __construct($value) {
        $this->val = Sql::quote($value);
    }

    public function toSql() {
        return $this->val;
    }
}