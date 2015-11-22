<?php if (!defined('INCLUDES_OK')) die ('Invalid SparkerPHP configuration.');
require_once('files.php');
require_once('outputHelper.php');

/**
 * Handles file uploading operations.
 */
class FileUpload extends File
{
	private $m_sTargetName = ''; //!< The name of the file in case want to save with same filename

	private static $m_iMaxSize = 10000000; //!< Maximum size of uploads to accept in bytes



	public function __construct($File, $iMaxSize = 0) {
		try {
			// Make sure were given a file upload array
			if (!array_key_exists('tmp_name', $File)
				|| $File['tmp_name'] == ''
				|| !array_key_exists('name', $File)
				|| $File['name'] == ''
				|| !array_key_exists('error', $File)
				)
				throw new Exception('Invalid file upload.');

			parent::__construct($File['tmp_name']);
			
			// Get name of file in case we want to keep name
			$this->m_sTargetName = $File['name'];

			// Check filesize
			if ($File['size'] > self::$m_iMaxSize)
				throw new Exception('The uploaded file '.$File['name'].' is too large. Must be less than '.outputHelper::convertBytesToHuman(self::$m_iMaxSize).'.');

			// Check errors
			switch ($File['error']) {
				case UPLOAD_ERR_OK:
    				break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new Exception('The uploaded file '.$File['name'].' is too large.');
				case UPLOAD_ERR_PARTIAL:
					throw new Exception('The uploaded file '.$File['name'].' was only partially uploaded.');
				case UPLOAD_ERR_NO_FILE:
					throw new Exception('No file was uploaded.');
				case UPLOAD_ERR_NO_TMP_DIR:
				case UPLOAD_ERR_CANT_WRITE:
					throw new Exception('Failed to write file '.$File['name'].' to disk.');
				default:
					throw new Exception('Unknown upload error on file '.$File['name'].'.');
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	public function __destruct() {
	}
	
	/**
	 * Saves file to given directory with given filename.
	 * @param   string   $sTargetDir   Directory to save file to. Default '../uploads'.
	 * @param   string   $sTargetName  File name to save file as. Default the name of the uploaded file.
	 * @param   bool     $bForce       Should overwrite file if exists. Default false.
	 * @return  bool                   If was able to save file
	 */
	public function save($sTargetDir = null, $sTargetName = null, $bForce = false) {
		// Default upload directory is ../uploads
		if ($sTargetDir == '')
			$sTargetDir = '../uploads/';
		// Default target name is the name when file was uploaded
		if ($sTargetName == '')
			$sTargetName = $this->m_sTargetName;
		return $this->move($sTargetDir.$sTargetName, $bForce);
	}

	/**
	 * Saves file to given directory with given filename overwriting if exists.
	 * @param   string   $sTargetDir   Directory to save file to. Default '../uploads'.
	 * @param   string   $sTargetName  File name to save file as. Default the name of the uploaded file.
	 * @return  bool                   If was able to save file
	 */
	public function overwrite($sTargetDir = null, $sTargetName = null) {
		return $this->save($sTargetDir, $sTargetName, true);
	}
	
	/**
	 * Sets the maximum allowable upload size for this and all subsequent uploads.
	 * @param     int   $iSize    Maximum upload size to accept in bytes
	 */
	public static function setMaxSize($iSize) {
		self::$m_iMaxSize = $iSize;
	}
	
	/**
	 * Returns the name of the file that was uploaded
	 * @return   string         File name
	 */
	public function getFilename() {
		return $this->m_sTargetName;
	}
	
	/**
	 * Returns if the target path already exists.
	 * @param  string   $sTarget  Path to check availability of
	 * @return bool               Does target already exist
	 */
	public function isTargetTaken($sTarget) {
		return file_exists($this->m_sTargetName);
	}
}
?>