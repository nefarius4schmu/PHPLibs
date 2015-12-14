<?php 
/**
 * Nefa Libs
 * Class DBTable
 * Database table parser for InnoDB table structures
 *
 * Copyright 2015 Steffen Lange
 * Licensed under the WTFPL license
 *
 * @author Steffen Lange
 * @version 1.0.0
 */
class DBTable {
    private $name;
    /** @var DBColumn[]  */
    private $cols = [];

    /**
     * @param string $name
     * @param array $info
     */
    function __construct($name, $info){
        $this->name = $name;
        foreach($info as $col){
            $dbcol = new DBColumn($col);
            $this->cols[$dbcol->field()] = $dbcol;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function is($name){return isset($this->cols[$name]);}

    /**
     * @param string $name
     * @return DBColumn
     */
    public function col($name){return $this->cols[$name];}

}

/**
 * Nefa Libs
 * Helper Class DBColumn
 * Database column parser for InnoDB table structures
 *
 * Copyright 2015 Steffen Lange
 * Licensed under the WTFPL license
 *
 * @author Steffen Lange
 * @version 1.0.0
 */
class DBColumn{
    const   TYPE_UNKNOWN = 0,
            TYPE_STRING = 1,
            TYPE_NUMBER = 2,
            TYPE_TIME = 3,
            FORMAT_TIMESTAMP = 'Y-m-d H:i:s'; //2015-12-13 23:07:17

    private $field, $type, $default, $pk, $null, $auto, $uniq, $unsigned;
    private $raw, $val;

    /**
     * @param array $data
     */
    function __construct($data){
        $this->field = $data['Field'];
        $this->type = $this->_type($data['Type']);
        $this->pk = $data['Key'] === 'PRI';
        $this->null = $data['Null'] !== 'NO';
        $this->auto = $data['Extra'] === 'auto_increment';
        $this->uniq = $data['Key'] === 'UNI';
        $this->unsigned = strpos($data['Type'], 'unsigned', 4) !== false;
        $this->default = $data['Default'];
    }

    /**
     * @return string
     */
    public function field(){return $this->field;}

    /**
     * @return mixed|null
     */
    public function get(){return $this->val;}

    /**
     * @return mixed
     */
    public function raw(){return $this->raw;}

    /**
     * @param mixed $value
     * @return bool
     */
    public function set($value){
        $this->raw = $value;
        $val = false;
        if($value !== null){
            switch($this->type){
                case self::TYPE_NUMBER:
                    if(is_numeric($value)) $val = $value*1;
                    break;
                case self::TYPE_UNKNOWN:
                case self::TYPE_STRING:
                    $val = (string)$value;
                    break;
                case self::TYPE_TIME:
                    if(is_int($value)){
                        $val = date(self::FORMAT_TIMESTAMP, $value);
                    }else if(is_string($value)){
                        $time = strtotime($value);
                        if($time !== false) $val = date(self::FORMAT_TIMESTAMP, $time);
                    }
                    break;
            }
        }else if(!$this->required()){
            $val = 'NULL';
        }

        if($val !== false){
            $this->val = $val;
            return true;
        }else{
            $this->val = null;
            return false;
        }
    }

    /**
     * @return bool
     */
    public function required(){
        return $this->pk === true || ($this->null === false && $this->default === null);
    }

    /**
     * @param string $type
     * @return int
     */
    private function _type($type){
        $base = strtolower(strstr($type, '(', true));
        switch($base){
            case 'char':
            case 'text':
            case 'string':
            case 'varchar':
                return self::TYPE_STRING;
            case 'bit':
            case 'int':
            case 'float':
                return self::TYPE_NUMBER;
            case 'date':
            case 'datetime':
            case 'timestamp':
                return self::TYPE_TIME;
            default:
                return self::TYPE_UNKNOWN;
        }
    }
}