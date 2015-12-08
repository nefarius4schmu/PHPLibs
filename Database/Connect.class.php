<?php
include_once(dirname(__FILE__).'/DB.class.php');

/**
 * Nefa Libs
 * Class Connect
 * basic database connection handler
 *
 * Copyright 2015 Steffen Lange
 * Licensed under the WTFPL license
 *
 * @requires DB
 * @author Steffen Lange
 * @version 1.0.0
 */
class Connect extends DB{

    const CONFIG = 'db.ini';

    const DB_NefaDB = 'nefadb';

    /** @var  PDO */
    private $connection;
    /** @var  PDOException */
    private $error;
    /** @var  string */
    private $lastQuery;
    /** @var bool */
    private $doDebug = false;

    function __construct($db, $config=null){
        try{
            $this->connection = parent::getLink($config !== null ? $config : self::CONFIG, $db);
        }catch(PDOException $e){
            throw new PDOException($e);
        }
    }

    public function debug($state=true){$this->doDebug = $state;}
    public function isDebug(){return $this->doDebug;}
    public function getLastError(){return $this->error;}
    public function getLastQuery(){return $this->lastQuery;}

    /**
     * @param string $q
     * @return array|false
     */
    public function select($q){
        $stmt = $this->query($q);
        if($stmt !== false) return $stmt->fetchAll();
        else return false;
    }

    /**
     * @param string $q
     * @param callable $fn
     * @param mixed $value (optional)
     * @return array|false
     * @throws Exception
     */
    public function map($q, $fn, $value=null){
        if(is_callable($fn)){
            $rows = [];
            $stmt = $this->query($q);
            if($stmt !== false){
                $i = 0;
                foreach($stmt as $row){
                    if($fn($rows, $row, $i, $value) !== false) $i++;
                    else break;
                }
                return $rows;
            }
        }else{
            throw new Exception('map function is no callable function');
        }
        return false;
    }

    /**
     * @param string $q
     * @return bool|false
     */
    public function listing($q){
        $stmt = $this->query($q);
        if($stmt !== false){
            $rows = [];
            while($row = $stmt->fetch()){
                $first = current($row);
                if($first !== false) $rows[] = $first;
                else break;
            }
            $stmt->closeCursor();
            return $rows;
        }else return false;
    }

    /**
     * @param string $q
     * @return bool|false
     */
    public function item($q){
        $stmt = $this->query($q);
        if($stmt !== false){
            $row = $stmt->fetch();
            $stmt->closeCursor();
            return $row;
        }else return false;
    }

    /**
     * @param string $q
     * @return bool|false
     */
    public function value($q){
        $item = $this->item($q);
        return is_array($item) ? current($item) : false;
    }

    /**
     * @param string $q
     * @param int $fetchStyle
     * @return PDOStatement|false
     */
    private function query($q, $fetchStyle=PDO::FETCH_ASSOC){
        $this->lastQuery = $q;
        if(!$this->isDebug()){
            $error = null;
            $stmt = null;
            try{
                $stmt = $this->connection->query($q, $fetchStyle);
                if($stmt === false) $error = new PDOException('query failed');
            }catch(PDOException $e){
                $error = $e;
            }
            $this->error = $error;
            if($error === null) return $stmt;
        }
        return false;
    }
}