<?php 
/**
 * Nefa Libs
 * Class DB
 * basic database connector
 *
 * based on
 * PHP.net
 * Author: kcleung at kcleung dot no-ip dot org
 * http://de2.php.net/manual/en/class.pdo.php#97682
 * last access: 2015-07-14
 *
 * @author Steffen Lange
 * @version 1.2.0
 */
class DB {

	const DEFAULT_CONFIG = 'config.ini';

    private static $links = [] ;

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
    public static function getLink($config=self::DEFAULT_CONFIG, $schema=null, $reconnect=false) {
        $key = $config.$schema;
        if (!$reconnect && self::is($key)) {
            return self::get($key);
        }
        Debug::v($config);
        $parse = parse_ini_file ( $config , true ) ;
		if($parse === false) return false;
		
        $driver = $parse [ "db_driver" ] ;
        $dsn = "${driver}:" ;
        $user = $parse [ "db_user" ] ;
        $password = $parse [ "db_password" ] ;
        $options = $parse [ "db_options" ] ;
        $attributes = $parse [ "db_attributes" ] ;

        foreach ( $parse [ "dsn" ] as $k => $v ) {
            $dsn .= "${k}=${v};" ;
        }

        if(is_string($schema)) $dsn .= 'dbname='.$schema;


		try{
			$link = new PDO ( $dsn, $user, $password, $options ) ;
		}catch(PDOException $e){
			throw new PDOException($e);
		}
        
        foreach ( $attributes as $k => $v ) {
            $link -> setAttribute ( constant ( "PDO::{$k}" )
                , constant ( "PDO::{$v}" ) ) ;
        }

		self::set($key, $link);
        return $link ;
    }
}