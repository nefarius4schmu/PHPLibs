<?php 
/**
 * Nefa Libs
 * Class DB
 * singleton database connector
 *
 * based on
 * PHP.net
 * Author: kcleung at kcleung dot no-ip dot org
 * http://de2.php.net/manual/en/class.pdo.php#97682
 * last access: 2015-07-14
 *
 * @author Steffen Lange
 * @version 1.2.1
 */
class DBConnect {

	const DEFAULT_CONFIG = 'config.ini';

    private static $links = [] ;
    private static $config;

	private static function is($db){return isset(self::$links[$db]) && self::$links[$db];}
	private static function get($db){return self::$links[$db];}
	private static function set($db, $link){self::$links[$db] = $link;}

    /**
     * @param string $config (optional)
     * @param string $schema (optional)
     * @param bool $reconnect (optional)
     * @return PDO
     * @throws PDOException
     */
    protected static final function getLink($config=self::DEFAULT_CONFIG, $schema=null, $reconnect=false) {
        $key = $config.$schema;
        if(!$reconnect && self::is($key)){
            return self::get($key);
        }

        $parse = parse_ini_file($config, true);
		if($parse === false) return false;

        $driver = $parse['db_driver'];
        $dsn = "${driver}:";
        $user = $parse['db_user'];
        $password = $parse['db_password'];
        $options = $parse['db_options'];
        $attributes = $parse['db_attributes'];

        if(is_string($schema)) $parse['dsn']['dbname'] = $schema;
        foreach($parse['dsn'] as $k=>$v){
            $dsn .= "${k}=${v};";
        }

		try{
			$link = new PDO($dsn, $user, $password, $options);
		}catch(PDOException $e){
			throw new PDOException($e);
		}
        
        foreach($attributes as $k=>$v){
            $link->setAttribute(constant("PDO::{$k}"), constant("PDO::{$v}"));
        }

		self::set($key, $link);
        self::$config = $parse;
        return $link;
    }

    protected static final function getConnectionSettings($key=null){
        if($key !== null) return self::$config[$key];
        else return self::$config;
    }
}