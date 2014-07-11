<?php
namespace QueryBuilder;
use Ptilz\Dbg;

/**
 * Boolean expression. Zero or more sub-expressions separated by a boolean operator (AND, OR).
 *
 * @see http://dev.mysql.com/doc/refman/5.7/en/expressions.html
 * @see http://dev.mysql.com/doc/refman/5.7/en/non-typed-operators.html
 * @property-read int $count Number of sub-expressions
 */
class BoolExpr extends Expr {
    /** @var string Boolean operator: OR, ||, XOR, AND, && */
    protected $operator;
    /** @var array Sub-expressions to be joined */
    protected $exprList;

    protected static $_operators = ['OR', '||', 'XOR', 'AND', '&&'];

    public function __construct($operator = 'AND', $exprList = []) {
        if(!in_array($operator, static::$_operators, true)) throw new \Exception("Invalid boolean operator: $operator");
        $this->operator = $operator;
        $this->exprList = $exprList;
    }

    public function add() {
        $args = func_get_args();
        if(count($args) === 1) {
            if($args[0] instanceof Expr) {
                $this->exprList[] = $args[0];
                return;
            } elseif(is_array($args[0])) {
                $args = $args[0];
            } else {
                throw new \Exception('Invalid expression type "' . Dbg::getType($args) . '"');
            }
        }
        switch(count($args)) {
            case 1:
                $this->exprList[] = $args[0] instanceof Expr ? $args[0] : QB::id($args[0]);
                break;
            case 2:
                $this->exprList[] = new ComparisonExpr('=', $args[0], $args[1]);
                break;
            case 3:
                $this->exprList[] = new ComparisonExpr($args[1], $args[0], $args[2]);
                break;
            default:
                throw new \Exception("Expected 1-3 args, got ".count($args));
        }
    }

    public function addMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->exprList = array_merge($this->exprList, $args);
        return $this;
    }

    public function toSql() {
        return implode(" $this->operator ",$this->exprList);
    }

    public function __get($name) {
        switch($name) {
            case 'count':
                return count($this->exprList);
        }
        throw new \Exception("Undefined property '$name'");
    }
}
