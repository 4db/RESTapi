<?php
/**
 * Class Model
 */
Abstract Class Model {
	/**
	 * @var PDO
	 */
	protected  $db;

	/**
	 * @var string response error
	 */
	public $error;

	/**
	 * @var string response data
	 */
	public $data;

	/**
	 * @var int response HTTP code
	 */
	public $code = 400;

	/**
	 * @var object ErrorLog
	 */
	protected $_errorLog;

	public function __construct() {
		$this->db = DB::getInstance();

		include_once('./models/ErrorLog.php');
		$this->_errorLog = new ErrorLog;
	}

	/**
	 * @param $model
	 * @return mixed
	 */
	public static function factory($model) {
		include_once("{$model}.php");
		return new $model;
	}
}