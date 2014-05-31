<?php

class QueryBuilder {
    /** @var string[] */
    protected $tables = [];
    /** @var string[] */
    protected $fields = [];
    /** @var Node */
    protected $where;

//    public static function create() {
//        return new static();
//    }

    public function from($table) {
        $args = func_get_args();
        $this->tables = count($args) === 1 && is_array($args[0]) ? $args[0] : $args;
        return $this;
    }

    public function select($columns) {
        $args = func_get_args();
        $this->fields = count($args) === 1 && is_array($args[0]) ? $args[0] : $args;
        return $this;
    }

    public function leftJoin() {}
    public function rigthJoin(){}
    public function innerJoin(){}
    public function outerJoin(){}

    public function where() {
        $args = func_get_args();
    }
}