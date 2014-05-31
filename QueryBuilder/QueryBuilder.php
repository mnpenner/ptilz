<?php
namespace QueryBuilder;

abstract class QueryBuilder {
    abstract public function toSql();

    public function __toString() {
        return $this->toSql();
    }
}