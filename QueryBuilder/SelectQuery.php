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

    /**
     * @return SelectQuery
     */
    public function select() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->columns = $args;
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function from() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->tables = $args;
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function leftJoin($table, $column1, $column2) {
        $this->joins[] = new JoinClause('LEFT JOIN', $table, [$column1, $column2]);
        return $this;
    }

    /**
     * @return SelectQuery
     */
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
     * @return SelectQuery
     */
    public function innerJoin($table, $column1, $column2) {
        $this->joins[] = new JoinClause('INNER JOIN', $table, [$column1, $column2]);
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function outerJoin($table, $column1, $column2) {
        $this->joins[] = new JoinClause('OUTER JOIN', $table, [$column1, $column2]);
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function addSelect($column, $alias=null) {
        if($alias) $this->columns[$alias] = $column;
        else $this->columns[] = $column;
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function addSelectMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->columns = array_merge($this->columns, $args);
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function orderBy() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->order = $args;
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function addOrderBy($col, $dir = SortDir::ASC) {
        $this->order[] = [$col, $dir];
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function addOrderByMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        $this->order = array_merge($this->order, $args);
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function where() {
        $args = func_get_args();
        if(count($args) === 1 && $args[0] instanceof BoolExpr) $this->where = $args[0];
        else foreach($args as $a) {
            $this->where->add($a);
        }
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function addWhere() {
        call_user_func_array([$this->where,'add'],func_get_args());
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function addWhereMulti() {
        $args = func_get_args();
        if(count($args) === 1 && is_array($args[0])) $args = $args[0];
        foreach($args as $a) {
            $this->where->add($a);
        }
        return $this;
    }

    /**
     * @return SelectQuery
     */
    public function toSql() {
        $sql = 'SELECT';
        if($this->columns) {
            $columns = [];
            foreach($this->columns as $alias => $col) {
                if(is_int($alias)) {
                    $columns[] = QB::id($col);
                } else {
                    $columns[] = QB::id($col) . ' AS ' . QB::id($alias);
                }
            }
            $sql .= ' '.implode(', ', $columns);
        }
        if($this->tables) {
            $tables = [];
            foreach($this->tables as $alias=>$tbl) {
                if(is_int($alias)) {
                    $tables[] = QB::id($tbl);
                } else {
                    $tables[] = QB::id($tbl).' AS '.QB::id($alias);
                }
                $sql.=' FROM '.implode(', ',$tables);
            }
        }
        if($this->where->count > 0) {
            $sql .= ' WHERE '.$this->where;
        }
        return $sql;
    }
}