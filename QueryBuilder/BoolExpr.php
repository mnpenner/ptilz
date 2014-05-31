<?php
namespace QueryBuilder;

/**
 * Boolean expression. Zero or more sub-expressions separated by a boolean operator (AND, OR).
 * @see http://dev.mysql.com/doc/refman/5.7/en/expressions.html
 */
class BoolExpr extends Expr {
    /** @var string Boolean operator: OR, ||, XOR, AND, && */
    protected $operator;
    /** @var array Sub-expressions to be joined */
    protected $exprList;

    protected static $_operators = ['OR','||','XOR','AND','&&'];

    public function __construct($operator = 'AND', $exprList = []) {
        if(!in_array($operator,static::$_operators,true)) throw new \Exception("Invalid boolean operator: $operator");
        $this->operator = $operator;
        $this->exprList = $exprList;
    }

    public function add() {
        $args = func_get_args();
        if(count($args) === 1 && $args[0] instanceof Expr) {
            $this->exprList[] = $args[0];
        } else {
            switch(count($args)) {
                case 1:
                    $this->exprList[] = QB::id($args[0]);
                    break;
                case 2:
                    $this->exprList[] = new ComparisonExpr('=',$args[0],$args[1]);
                    break;
                case 3:
                    $this->exprList[] = new ComparisonExpr($args[2],$args[0],$args[1]);
                    break;
            }
        }
        throw new \Exception("Unhandled");
    }

    public function addMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->exprList = array_merge($this->exprList, $args);
        return $this;
    }
}
