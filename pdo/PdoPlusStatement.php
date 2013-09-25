<?php

class PdoPlusStatement extends PDOStatement {
    protected function __construct() {}

    /**
     * @param array|mixed $input_parameters An array of values with as many elements as there are bound parameters in the SQL statement being executed, or one or more non-array arguments to be matched with sequential parameter markers.
     * @throws PDOException
     * @return PdoPlusStatement
     */
    public function execute($input_parameters=null) {
        $args = func_get_args();
        $argc = func_num_args();
        if($argc===0) {
            parent::execute();
        } else {
            if($argc===1 && is_array($args[0])) {
                $args = $args[0];
            }
            parent::execute($args);
        }
        return $this;
    }

    /**
     * Returns an array containing all of the remaining rows in the result set
     * @return array An associative array using the first column as the key and the remainder as associative values
     */
    public function fetchKeyAssoc() {
        return array_map('reset', $this->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC));
    }
}
