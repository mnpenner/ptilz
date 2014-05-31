<?php
namespace QueryBuilder;

class Identifier extends Expr {
    protected $id;

    public function __construct($id, $forbidQualified = false) {
        $this->id = '`' . str_replace('`', '``', $id) . '`';
        if(!$forbidQualified) $this->id = str_replace('.', '`.`', $this->id);
    }
}