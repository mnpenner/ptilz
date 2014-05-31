<?php
namespace QueryBuilder;

class ComparisonExpr extends Expr {
    /** @var string Comparison operator: = | >= | > | <= | < | <> | !=  */
    protected $operator;
    /** @var Expr */
    protected $left;
    /** @var Expr */
    protected $right;


    protected static $_operators = ['=','>=','>','<=','<','<>','!='];

    public function __construct($operator, $left, $right) {
        if(!in_array($operator,static::$_operators,true)) throw new \Exception("Invalid comparison operator: $operator");
        $this->operator = $operator;
        $this->left = $left instanceof Expr ? $left : QB::id($left);
        $this->right = $right instanceof Expr ? $right : QB::val($right);
    }
}