<?php
namespace QueryBuilder;

class JoinClause {
    /** @var string */
    protected $type;
    /** @var string|array */
    protected $table;
    /** @var BoolExpr */
    protected $where;

    public function __construct($type,$table,$where) {
        $this->type = $type;
        $this->table = $table;
        $this->where = $where;
    }
}