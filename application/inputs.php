<?php if (!defined('INCLUDES_OK')) die ('Invalid SparkerPHP configuration.');
require_once('fileUpload.php');
require_once('schema.php');

class Inputs
{
	private static $m_instance = null; // Pointer to this instance of schema class
	private $m_Post = array(); // Array of post vars
	private $m_Get = array(); // Array of get vars
	private $m_Session = array(); // Array of session vars
	private $m_Cookie = array(); // Array of cookie vars
	private $m_File = array(); // Array of file vars
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
			self::$m_instance = new Inputs();
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
	 * Get file vars
	 */
	public function file($param) {
		$value = null;
		if (array_key_exists($param, $this->m_File)) {
			$value = $this->m_File[$param]['value'];
		}
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
			case 'file':
				if (array_key_exists($param, $this->m_File))
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
			case 'file':
				if (array_key_exists($param, $this->m_File))
					$bIsClean = $this->m_File[$param]['clean'];
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
			case 'file':
				if (array_key_exists($param, $this->m_File))
					$err = $this->m_File[$param]['error'];
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
		foreach ($_FILES as $k => $v) {
			$this->processFileInput($k, $v);
			$this->m_File[':'.$k]['value'] = $v;
		}
		unset($_FILES);
	}

	/**
	 * Processes each intput file and extracts relevant data
	 * @param    string  $index   Input name
	 * @param    mixed   $file    File input object or array of file input objects if input name array
	 */
	private function processFileInput($index, $file) {
		// If multi file upload
		if (is_array($file['tmp_name'])) {
			// Convert arrays of fields to arrays of files
			foreach ($file as $sFieldIndex => $sField) {
				foreach ($sField as $sFileIndex => $sFieldValue)
					$Files[$sFileIndex][$sFieldIndex] = $sFieldValue;
			}
			// Recurse until individual file with each input being suffixed with -#
			for ($ii = 0; $ii < sizeof($Files); $ii++)
				$this->processFileInput("$index-$ii", $Files[$ii]);
		}
		else {
			if (__SAFESCHEMA__) {
				// Clean inputs
				$sFileName = $this->m_pSchema->clean_by_element($file['name'], 'filename');
				$sFileTmp = $file['tmp_name'];
				$sFileSize = $file['size'];
				$sFileErr = $file['error'];
				$this->m_File[$index] = array('error'=>'', 'value'=>'', 'clean'=>0);
				
				// If invalid file name
				if (!$sFileName['clean']) {
					$this->m_File[$index]['error'] = $sFileName['error'];
					return;
				}
				
				// Setup file parameters
				$f['tmp_name'] = $sFileTmp;
				$f['size'] = $sFileSize;
				$f['error'] = $sFileErr;
				$f['name'] = $sFileName['value'];

				// Try to create new file upload object
				try {
					$this->m_File[$index]['value'] = new FileUpload($f);
					$this->m_File[$index]['clean'] = 1;
				}
				// If exception, just don't add input
				catch (Exception $e) {
					//$this->m_File[$index]['error'] = $e->getMessage();
					unset($this->m_File[$index]);
				}
			}
			else {
				$this->m_File[$index] = $file;
			}
		}
	}
}
?>
