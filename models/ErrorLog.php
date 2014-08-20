<?php
/**
 * Class Model ErrorLog
 * table error_log
 */
Class ErrorLog extends Model {
	public function __construct() {
		$this->db = DB::getInstance();
	}
	public function add($message) {
		$pathInfo = "";
		if (isset($_SERVER['PATH_INFO'])) {
			$pathInfo = $_SERVER['PATH_INFO'];
		}
		$server = print_r($_SERVER, true);

		$sql = "INSERT INTO `error_log` (`message`, `path_info`,`server_data`) VALUES ('{$message}','{$pathInfo}', '{$server}' )";
		$query = $this->db->prepare($sql);

		if ( !$query->execute() ) {
			//All very bad
			header('HTTP/1.1 404 Not Found');
			die();
		}
	}
}