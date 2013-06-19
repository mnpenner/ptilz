<?php

class PdoPlusStatement extends PDOStatement {
    protected function __construct() {}

    /**
     * @param array|mixed $input_parameters An array of values with as many elements as there are bound parameters in the SQL statement being executed, or a single value to be matched with a single parameter marker.
     * @throws PDOException
     * @return PdoPlusStatement
     */
    public function execute($input_parameters=array()) {
        if(!is_array($input_parameters)) $input_parameters = array($input_parameters);
        if(parent::execute($input_parameters)===false) {
            throw new PDOException("Failed to execute statement");
        }
        return $this;
    }
}

