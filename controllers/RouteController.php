<?php
include_once('./models/Model.php');
/**
 * Class RouteController
 * Defines methods action from URI
 */
Class RouteController {

	/**
	 * REQUEST_METHOD
	 * @var
	 */
	protected $_method;

	/**
	 * Defines uri params
	 * @var array
	 */
	protected $_segments;

	/**
	 * First uri param, can be ClassName
	 * @var mixed
	 */
	protected $_class;

	/**
	 * Second uri param
	 * @var
	 */
	protected $_action;

	/**
	 * Response data
	 * @var array
	 */
	public $data = array();

	/**
	 * Response error
	 * @var string
	 */
	public $error = '';

	/**
	 * Response code
	 * @var int
	 */
	public $code = 200;

	/**
	 * Map className, Method, actionFunction
	 * @var array
	 */
	protected $classMethodMap = array(
		'users' => array(
			'GET' => "usersGETmap",
			'POST'=> "usersPOSTmap",
		),
	);

	public function __construct() {
		//Init URI params
		$this->_method = $_SERVER['REQUEST_METHOD'];
		$this->_segments = explode('/' , $_SERVER['PATH_INFO']);

		if (!empty($this->_segments)) {
			array_shift($this->_segments); // Delete first element "/"
			if (isset($this->_segments[0])) {
				$this->_class = array_shift($this->_segments);
			}
			if (isset($this->_segments[0])) {
				$this->_action = array_shift($this->_segments);
			}
		}
	}

	/**
	 * Start controller method.
	 * launches Methods from $classMethodMap
	 */
	public function map() {
		try {
			if (!isset($this->classMethodMap[$this->_class])) {
				throw new Exception('Class not Found: ' . $this->_class);
			}

			if (!isset($this->classMethodMap[$this->_class][$this->_method])) {
				throw new Exception('Not Found method ' . $this->_method . ' in class ' . $this->_class);
			}

			if ( !$this->{$this->classMethodMap[$this->_class][$this->_method]}() ) {
				throw new Exception('Not Found action function' . $this->_action . ' in method ' . $this->_method . 'in class ' . $this->_class);
			}
		} catch(Exception $e) {
			header('HTTP/1.1 404 Not Found');
			die();
		}

		include_once('./view/Vjson.php');
		Vjson::show($this->data, $this->error, $this->code);
	}

	/**
	 * Logic action method for POST /users...
	 * @return bool
	 */
	public function usersPOSTmap() {
		/** @var Users $model */
		$model = Model::factory('Users');

		// URI /users
		if (empty($this->_action) && empty($this->_segments)) {
			if (!$model->createNewUser()) {
				return false;
			}
			$this->data = $model->data;
			$this->code = $model->code;
			return true;
		}

		// URI /users/1/score/10
		if (
			count($this->_segments) == 2 &&
			$this->_segments[0] == "score" &&
			is_numeric($this->_action) &&
			is_numeric($this->_segments[1])
		) {
			if (!$model->addScore($this->_action, $this->_segments[1])) {
				$this->error = $model->error;
				$this->code = $model->code;
			}
			return true;
		}
		return false;
	}

	/**
	 * Logic action method for GET /users...
	 * @return bool
	 */
	public function usersGETmap() {
		/** @var Users $model */
		$model = Model::factory('Users');

		// URI /users
		if (empty($this->_action) && empty($this->_segments)) {
			if (!$model->getTotalUsers()) {
				$this->code = 404;
				return false;
			}
			$this->data = $model->data;
			return true;
		}

		// URI /users/1
		if (is_numeric($this->_action) && empty($this->_segments)) {
			if (!$model->getUserScore($this->_action)) {
				$this->error = $model->error;
				$this->code = $model->code;
			}
			else {
				$this->data = $model->data;
			}
			return true;
		}

		// URI /users/byDate
		if ($this->_action == 'byDate' && empty($this->_segments)) {
			if (!$model->getTotalUsers(date('Y-m-d'))) {
				$this->code = 404;
				return false;
			}
			$this->data = $model->data;
			return true;
		}

		// URI /users/byDate/2014/1/12
		if (
			$this->_action == 'byDate' &&
			count($this->_segments) == 3
		) {
			if (
				strlen($this->_segments['0']) == 4 && is_numeric($this->_segments['0']) && //Year
				is_numeric($this->_segments['1']) && !empty($this->_segments['1']) && $this->_segments['1'] <= 12 && //Month
				is_numeric($this->_segments['2']) && !empty($this->_segments['2']) && $this->_segments['2'] <= 31 //Date
			) {
				if (!$model->getTotalUsers($this->_segments['0'] . '-' . $this->_segments['1'] . '-' . $this->_segments['2'])) {
					$this->code = 404;
					return false;
				}
				$this->data = $model->data;
			}
			else {
				$this->code = 400;
				$this->error= 'Incorrect date';
			}
			return true;
		}

		// URI /users/listByScore/limit/10
		if (
			$this->_action == 'listByScore' &&
			count($this->_segments) == 2 &&
			$this->_segments[0] == 'limit' &&
			is_numeric($this->_segments[1])
		) {
			if (!$model->getTopUsers($this->_segments[1])) {
				$this->code = 404;
				return false;
			}
			$this->data = $model->data;
			return true;
		}
		return false;
	}
}