<?php
namespace QueryBuilder;

class SelectQuery extends QueryBuilder {
    protected $columns;
    protected $order;
    protected $tables;
    protected $joins;
    protected $where;

    public function __construct() {
        $this->columns = ['*'];
        $this->order = [];
        $this->tables = [];
        $this->joins = [];
        $this->where = new BoolExpr;
    }

    public function select() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->columns = $args;
        return $this;
    }

    public function from() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->tables = $args;
        return $this;
    }

    public function leftJoin($table, $column1, $column2) {
        $this->joins[] = new JoinClause('LEFT JOIN', $table, [$column1, $column2]);
        return $this;
    }

    public function rightJoin($table, $column1, $column2) {
        $this->joins[] = new JoinClause('RIGHT JOIN', $table, [$column1, $column2]);
        return $this;
    }

    /**
     * JOIN, CROSS JOIN, and INNER JOIN are syntactic equivalents.
     *
     * @param string $table
     * @param string $column1
     * @param string $column2
     * @return $this
     */
    public function innerJoin($table, $column1, $column2) {
        $this->joins[] = new JoinClause('INNER JOIN', $table, [$column1, $column2]);
        return $this;
    }

    public function outerJoin($table, $column1, $column2) {
        $this->joins[] = new JoinClause('OUTER JOIN', $table, [$column1, $column2]);
        return $this;
    }

    public function addSelect($column, $alias=null) {
        if($alias) $this->columns[$alias] = $column;
        else $this->columns[] = $column;
        return $this;
    }

    public function addSelectMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->columns = array_merge($this->columns, $args);
        return $this;
    }

    public function orderBy() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->order = $args;
        return $this;
    }

    public function addOrderBy($col, $dir = SortDir::ASC) {
        $this->order[] = [$col, $dir];
        return $this;
    }

    public function addOrderByMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->order = array_merge($this->order, $args);
        return $this;
    }

    public function where() {
        $args = func_get_args();
        if(count($args) === 1 && $args[0] instanceof BoolExpr) $this->where = $args[0];
        else foreach($args as $a) {
            $this->where->add($a);
        }
        return $this;
    }

    public function addWhere() {
        call_user_func_array([$this->where,'add'],func_get_args());
        return $this;
    }

    public function addWhereMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        foreach($args as $a) {
            $this->where->add($a);
        }
        return $this;
    }
}