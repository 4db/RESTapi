<?php

/**
 * Class view Vjson
 */
class Vjson {
	/**
	 * Show headers, JSON data (if isset)
	 * @param $data
	 * @param $error
	 * @param $code
	 */
	public static function show($data, $error, $code) {
		switch($code) {
			case 201:
				header('HTTP/1.1 201 Created');
				break;
			case 400:
				header('HTTP/1.1 400 Bad Request');
				break;
			case 200:
				break;
		}

		if (!empty($data) || !empty($error)) {
			echo json_encode(
				array(
					'data' => $data,
					'error'=> $error
				)
			);
		}
	}
}