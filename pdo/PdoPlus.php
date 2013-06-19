<?php

class PdoPlus extends PDO {
    /**
     * @param string $identifier
     * @throws Exception
     * @return string
     */
    protected static function quote_identifier($identifier) {
        throw new Exception('Not supported on base class');
    }

    protected static function is_assoc($arr) {
        $i = 0;
        foreach($arr as $k => $_) {
            if($k !== $i++) return true;
        }
        return false;
    }

    public function count($table) {
        return (int)$this->query('SELECT COUNT(*) FROM '.static::quote_identifier($table))->fetchColumn();
    }

    public function truncate($table) {
        return $this->exec('TRUNCATE '.static::quote_identifier($table));
    }

    /**
     * Prepare and execute a statement
     *
     * @param string $statement SQL query
     * @param mixed|array $input_parameters An array of values with as many elements as there are bound parameters in the SQL statement being executed, or a single value to be mapped to a single placeholder.
     * @return PdoPlusStatement
     * @deprecated You may now chain methods together such as $pdo->prepare("SELECT * FROM x WHERE y=?")->execute(10)->fetch()
     */
    public function prepare_execute($statement, $input_parameters=array()) {
        $stmt = $this->prepare($statement);
        if(!is_array($input_parameters)) $input_parameters = array($input_parameters);
        $stmt->execute($input_parameters);
        return $stmt;
    }

    public function quote_array($array, $add_parens=true) {
        $out = array();
        foreach($array as $v) {
            $out[] = $this->quote($v);
        }
        $sql = implode(', ',$out);
        if($add_parens) {
            $sql = "($sql)";
        }
        return $sql;
    }

    public function get_columns($database, $table) {
        return $this->prepare_execute('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=?', array($database, $table))->fetchAll(PDO::FETCH_COLUMN);
    }

    public function interactive() {
        while(true) {
            $sql = readline('SQL> ');
            if($sql==='q') break;
            readline_add_history($sql);
            try {
                $stmt = $this->query($sql);
                if($r = $stmt->fetch()) {
                    do {
                        WXU::pprint_cli($r);
                    } while($r = $stmt->fetch());
                } else {
                    echo 'No results.'.PHP_EOL;
                }
            } catch(PDOException $e) {
                echo $e->getMessage().PHP_EOL;
            }
        }
    }
}

