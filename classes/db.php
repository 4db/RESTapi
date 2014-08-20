<?php

/**
 * Class DB
 */
class DB {

	/**
	 * @var
	 */
	private static $objInstance;

	/*
	 * Class Constructor - Create a new database connection if one doesn't exist
	 * Set to private so no-one can create a new instance via ' = new DB();'
	 */
	private function __construct() {}

	/*
	 * Like the constructor, we make __clone private so nobody can clone the instance
	 */
	private function __clone() {}

	/**
	 * Returns DB instance or create initial connection
	 * @return PDO
	 */
	public static function getInstance() {
		$db = include_once('./config/mysqldb.php');
		if(!self::$objInstance){
			try {
				self::$objInstance = new PDO("mysql:host={$db['host']};dbname={$db['dbname']};characterset={$db['encoding']}", $db['user'], $db['pwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES {$db['encoding']}"));
				self::$objInstance->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
				self::$objInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				header('HTTP/1.1 404 Not Found');
				die();
			}
		}
		return self::$objInstance;
	}

	/**
	 * Passes on any static calls to this class onto the singleton PDO instance
	 * @param $chrMethod
	 * @param $arrArguments
	 * @return mixed
	 */
	final public static function __callStatic( $chrMethod, $arrArguments ) {
		$objInstance = self::getInstance();
		return call_user_func_array(array($objInstance, $chrMethod), $arrArguments);
	}
}





