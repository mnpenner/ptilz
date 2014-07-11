<?php

use Ptilz\Exceptions\ArgumentTypeException;

class MsPdo extends PdoPlus {

    function __construct($host, $database_name, $username, $password, $options = null) {
        if($options === null) $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
        $dsn = "dblib:host=$host;dbname=$database_name";
        parent::__construct($dsn, $username, $password, $options);
    }

    protected static function quote_identifier($field) {
        return '['.str_replace(']',']]',$field).']';
    }

    public function select_all($table, $fields='*', $limit=null) {
        $sql = 'SELECT ';
        if(is_array($fields)) {
            $field_arr = array();
            foreach($fields as $f) {
                $field_arr[] = self::quote_identifier($f);
            }
            $field_str = implode(', ', $field_arr);
        } elseif($fields===null) {
            $field_str = '*';
        } elseif(is_string($fields)) {
            $field_str = $fields;
        } else throw new ArgumentTypeException('fields');
        if($limit!==null) {
            $sql .= "TOP $limit ";
        }
        $table_str = self::quote_identifier($table);
        $sql .= "$field_str FROM $table_str";
        return $this->query($sql);
    }
}
