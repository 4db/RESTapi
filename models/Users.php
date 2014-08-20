<?php
/**
 * Class model Users, table users
 */
Class Users extends Model{
	/**
	 * Create new user, write $code, $data.
	 * @return bool
	 */
	public function createNewUser() {
		try {
			$sql = "INSERT INTO `users` (`id`) VALUES (NULL)";
			$query = $this->db->prepare($sql);

			if (!$query->execute()) {
				throw new PDOException('Error in SQL ' . $sql);
			}
			$this->code = 201;
		} catch (PDOException $e) {
			$this->_errorLog->add($e->getMessage());
			return false;
		}
		$this->data = array('id'=>$this->db->lastInsertId());
		return true;
	}

	/**
	 * Check isset user_id & add score
	 * @param $userId
	 * @param $score
	 * @return bool
	 */
	public function addScore($userId, $score) {
		try {
			if (!$this->issetUser($userId)) {
				throw new PDOException();
			}

			include_once('./models/Score.php');
			$scoreObj = new Score();

			if (!$scoreObj->add($userId, $score)) {
				throw new PDOException();
			}
		} catch (PDOException $e) {
			$this->error = 'Not found user ID:' . $userId;
			$this->code = 400;
			return false;
		}
		return true;
	}

	/**
	 * Check isset user_id
	 * @param $id
	 * @return bool
	 */
	public function issetUser($id) {
		try {
			$sql = "SELECT id FROM users WHERE id = '{$id}'";
			$query = $this->db->prepare($sql);

			if (!$query->execute()) {
				throw new PDOException('Error in SQL ' . $sql);
			}
			$res = $query->fetchColumn();
			if (empty($res)) {
				return false;
			}
		} catch (PDOException $e) {
			$this->_errorLog->add($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * @param null $date
	 * @return bool
	 */
	public function getTotalUsers($date = null) {
		try {
			$sql = "SELECT COUNT(u.id) AS total FROM `users` u";
			if (!empty($date)) {
				$sql = "
					SELECT COUNT(distinct u.id) AS total FROM `users` u
					LEFT JOIN score s ON u.id = s.user_id
					WHERE DATE(s.created) = '{$date}'
				";
			}
			$query = $this->db->prepare($sql);

			if (!$query->execute()) {
				throw new PDOException('Error in SQL ' . $sql);
			}
			$this->data['total'] = $query->fetchColumn();
		} catch (PDOException $e) {
			$this->_errorLog->add($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * @param $userId
	 * @return bool
	 */
	public function getUserScore($userId) {
		try {
			if (!$this->issetUser($userId)) {
				throw new PDOException();
			}

			include_once('./models/Score.php');
			$scoreObj = new Score();

			$result = $scoreObj->getUserInfo($userId);
			if (!$result) {
				throw new PDOException();
			}
			$this->data = $result;
		} catch (PDOException $e) {
			$this->error = 'Not found user ID:' . $userId;
			$this->code = 400;
			return false;
		}
		return true;
	}

	/**
	 * @param $limit
	 * @return bool
	 */
	public function getTopUsers($limit) {
		include_once('./models/Score.php');
		$scoreObj = new Score();

		$result = $scoreObj->getUsersByScore($limit);
		if (!$result) {
			return false;
		}
		$this->data = $result;
		return true;
	}
}