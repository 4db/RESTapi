<?php

/**
 * Class model Score, table score
 */
Class Score extends Model{
	/**
	 * Add score
	 * @param $userId
	 * @param $score
	 * @return bool
	 */
	public function add($userId, $score) {
		try {
			$sql = "INSERT INTO `score` (`user_id`, `score`) VALUES ('{$userId}', '{$score}')";

			$query = $this->db->prepare($sql);
			if(!$query->execute()){
				throw new PDOException('Error in SQL ' . $sql);
			}
		} catch (PDOException $e) {
			$this->_errorLog->add($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * @param $userId
	 * @return array|bool
	 */
	public function getUserInfo($userId) {
		try {
			$sql = "SELECT SUM(score) AS score FROM score WHERE user_id = '{$userId}' LIMIT 1";

			$query = $this->db->prepare($sql);
			if (!$query->execute()) {
				throw new PDOException('Error in SQL ' . $sql);
			}
			$score = 0;
			$result = $query->fetchColumn();
			if (is_numeric($result)) {
				$score = $result;
			}
			return array(
				'id' => $userId,
				'score' => $score
			);
		} catch (PDOException $e) {
			$this->_errorLog->add($e->getMessage());
			return false;
		}
	}

	/**
	 * @param $limit
	 * @return bool
	 */
	public function getUsersByScore($limit) {
		try {
			$sql = "SELECT user_id AS id, SUM(score) AS score
					 FROM score
					GROUP BY user_id
					ORDER BY score DESC
					LIMIT $limit
					";

			$query = $this->db->prepare($sql);
			if (!$query->execute()) {
				throw new PDOException('Error in SQL ' . $sql);
			}
			return $query->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			$this->_errorLog->add($e->getMessage());
			return false;
		}
	}
}