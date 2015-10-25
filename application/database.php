<?php
class DB {
	private $m_db         = null;
	private $m_sQuery     = '';
	private $m_LastQuery  = null;
	private $m_sLastQuery = '';

	public function __construct() {
		try {
			// NOTE: apt-get install php5-sqlite
			$this->m_db = new PDO(__DATABASE__);
		}
		catch (PDOException $exception) {
			echo 'Error: '.$exception->getMessage().'<br>';
			die('Cannot connect to database. Please try again later.');
		}
	}

	/**
	 * Gets results array for a given a query string and array of param replacements
	 */
	public function select($sQuery, $sParams) {
		$s = $this->m_db->prepare($sQuery);
		foreach ($sParams as $k => $v) {
			$s->bindValue($k, $v);
		}
		$s->execute();
		$this->m_sLastQuery = $sQuery;
		$this->m_LastQuery = $s;

		if (strpos($sQuery, 'LIMIT 1') > 0) {
			return $s->fetch();
		}
		else {
			return $s->fetchAll();
		}
	}

	/**
	 * Updates record(s) in database
	 */
	public function update($sQuery, $sParams) {
		$s = $this->m_db->prepare($sQuery);
		foreach ($sParams as $k => $v) {
			$s->bindValue($k, $v);
		}
		$s->execute();
		$this->m_sLastQuery = $sQuery;
		$this->m_LastQuery = $s;

		return $s->rowCount();
	}

	/**
	 * Deletes record(s) in database
	 */
	public function delete($sQuery, $sParams) {
		$s = $this->m_db->prepare($sQuery);
		foreach ($sParams as $k => $v) {
			$s->bindValue($k, $v);
		}
		$s->execute();
		$this->m_sLastQuery = $sQuery;
		$this->m_LastQuery = $s;

		return $s->rowCount();
	}
}
?>
