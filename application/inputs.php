<?php
require_once('schema.php');

class Inputs
{
	private static $m_instance = null; // Pointer to this instance of schema class
	private $m_Post = array(); // Array of post vars
	private $m_Get = array(); // Array of get vars
	private $m_Session = array(); // Array of session vars
	private $m_Cookie = array(); // Array of cookie vars
	private $m_pSchema = null; // Pointer to schema object



	private function __construct() {
		$this->m_pSchema = Schema::getInstance();
		$this->processParams();
	}
	public function __destruct() {
		$this->m_instance = null;
	}
	public static function getInstance() {
		if (self::$m_instance === null) {
			self::$m_instance = new inputs();
		}
		return self::$m_instance;
	}

	/**
	 * Get get vars
	 */
	public function get($param) {
		$value = null;
		if (array_key_exists($param, $this->m_Get))
			$value = $this->m_Get[$param]['value'];
		return $value;
	}

	/**
	 * Get post vars
	 */
	public function post($param) {
		$value = null;
		if (array_key_exists($param, $this->m_Post))
			$value = $this->m_Post[$param]['value'];
		return $value;
	}

	/**
	 * Get session vars
	 */
	public function session($param) {
		$value = null;
		if (array_key_exists($param, $this->m_Session))
			$value = $this->m_Session[$param]['value'];
		return $value;
	}

	/**
	 * Get cookie vars
	 */
	public function cookie($param) {
		$value = null;
		if (array_key_exists($param, $this->m_Cookie))
			$value = $this->m_Cookie[$param]['value'];
		return $value;
	}

	/**
	 * Get if data is set
	 *
	 * @param    string   $type   post, get, session, cookie, etc.
	 * @param    string   $param  name of variable
	 * @return   bool             If value is set
	 */
	public function exists($type, $param) {
		$bIsSet = false;
		switch($type) {
			case 'get':
				if (array_key_exists($param, $this->m_Get))
					$bIsSet = true;
				break;
			case 'post':
				if (array_key_exists($param, $this->m_Post))
					$bIsSet = true;
				break;
			case 'session':
				if (array_key_exists($param, $this->m_Session))
					$bIsSet = true;
				break;
			case 'cookie':
				if (array_key_exists($param, $this->m_Cookie))
					$bIsSet = true;
				break;
			default:
				break;
		}
		return $bIsSet;
	}

	/**
	 * Get if data is clean
	 *
	 * @param    string   $type   post, get, session, cookie, etc.
	 * @param    string   $param  name of variable
	 * @return   bool             If value is clean
	 */
	public function isClean($type, $param) {
		$bIsClean = '';
		switch($type) {
			case 'get':
				if (array_key_exists($param, $this->m_Get))
					$bIsClean = $this->m_Get[$param]['clean'];
				break;
			case 'post':
				if (array_key_exists($param, $this->m_Post))
					$bIsClean = $this->m_Post[$param]['clean'];
				break;
			case 'session':
				if (array_key_exists($param, $this->m_Session))
					$bIsClean = $this->m_Session[$param]['clean'];
				break;
			case 'cookie':
				if (array_key_exists($param, $this->m_Cookie))
					$bIsClean = $this->m_Cookie[$param]['clean'];
				break;
			default:
				break;
		}
		return $bIsClean;
	}

	/**
	 * Get error message for a given variable
	 *
	 * @param    string   $type   post, get, session, cookie, etc.
	 * @param    string   $param  name of variable
	 * @return   string           error message
	 */
	public function getError($type, $param) {
		$err = '';
		switch($type) {
			case 'get':
				if (array_key_exists($param, $this->m_Get))
					$err = $this->m_Get[$param]['error'];
				break;
			case 'post':
				if (array_key_exists($param, $this->m_Post))
					$err = $this->m_Post[$param]['error'];
				break;
			case 'session':
				if (array_key_exists($param, $this->m_Session))
					$err = $this->m_Session[$param]['error'];
				break;
			case 'cookie':
				if (array_key_exists($param, $this->m_Cookie))
					$err = $this->m_Cookie[$param]['error'];
				break;
			default:
				break;
		}
		return $err;
	}


	/**
	 * Loads all sanitized $_GET and $_POST parameters into the application object and makes
	 * global $_GET and $_POST unavailable so don't accidentally use dirty value.
	 */
	private function processParams() {
		foreach ($_GET as $k => $v) {
			if (__SAFESCHEMA__) {
				$this->m_Get[$k] = $this->m_pSchema->clean_by_element($v, $k);
			}
			else {
				$this->m_Get[$k] = $v;
			}
			$this->m_Get[':'.$k]['value'] = $v;
		}
		unset($_GET);
		foreach ($_POST as $k => $v) {
			if (__SAFESCHEMA__) {
				$this->m_Post[$k] = $this->m_pSchema->clean_by_element($v, $k);
			}
			else {
				$this->m_Post[$k] = $v;
			}
			$this->m_Post[':'.$k]['value'] = $v;
		}
		unset($_POST);
		if (isset($_SESSION)) {
			foreach ($_SESSION as $k => $v) {
				if (__SAFESCHEMA__) {
					$this->m_Session[$k] = $this->m_pSchema->clean_by_element($v, $k);
				}
				else {
					$this->m_Session[$k] = $v;
				}
				$this->m_Session[':'.$k]['value'] = $v;
			}
			unset($_SESSION);
		}
		foreach ($_COOKIE as $k => $v) {
			if (__SAFESCHEMA__) {
				$this->m_Cookie[$k] = $this->m_pSchema->clean_by_element($v, $k);
			}
			else {
				$this->m_Cookie[$k] = $v;
			}
			$this->m_Cookie[':'.$k]['value'] = $v;
		}
		unset($_COOKIE);
	}
}
?>
