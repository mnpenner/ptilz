<?php
namespace QueryBuilder;

class SubQuery extends SelectQuery {
    /** @var string|null EXISTS, ANY, ALL or `null` */
    protected $type;

    public function __construct($type=null) {
        parent::__construct();
        $this->type = $type;
    }

    public function toSql() {
        return ($this->type?:'').'('.parent::toSql().')';
    }
}