<?php
namespace QueryBuilder;

use Ptilz\Sql;

class Identifier extends Expr {
    protected $id;

    public function __construct($id, $forbidQualified = false) {
        $this->id = Sql::escapeId($id, $forbidQualified);
    }

    public function toSql() {
        return $this->id;
    }
}