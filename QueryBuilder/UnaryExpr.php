<?php
namespace QueryBuilder;

use Dbg;


class UnaryExpr extends Expr {
    /** @var string Unary operator */
    protected $operator;
    /** @var array Expression to apply unary operator against */
    protected $expr;

    protected static $_operators = ['!', 'NOT', '-', '~', '+'];

    public function __construct($operator, $expr) {
        if(!in_array($operator, static::$_operators, true)) throw new \Exception("Invalid unary operator: $operator");
        $this->operator = $operator;
        $this->expr = $expr;
    }


    public function toSql() {
        return $this->operator === 'NOT'
            ? $this->operator . ' ' . $this->expr
            : $this->operator . $this->expr;
    }
}
