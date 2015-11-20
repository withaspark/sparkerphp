<?php if (!defined('INCLUDES_OK')) die ('Invalid SparkerPHP configuration.');

/**
 * Handles file operations like save, move, delete, etc.
 */
class File
{
	private $m_sFile = ''; //!< Path to file



	public function __construct($sFile = null) {
		if (!file_exists($sFile))
			throw new Exception("Unable to find file $sFile. Verify file exists.");
		else if (!is_readable($sFile))
			throw new Exception("Unable to read file $sFile. Verify file is readable.");

		$this->m_sFile = $sFile;
	}

	public function __destruct() {
	}

	/**
	 * Moves file from current location to target
	 * @param    string   $sTarget   Path to move file to
	 * @param    bool     $bForce    Should overwrite file if exists
	 * @return   bool                If was able to move the file
	 */
	public function move($sTarget, $bForce = false) {
		$sDir = pathinfo($sTarget, PATHINFO_DIRNAME);

		// Make sure the directory is writable
		if (!is_dir($sDir) || !is_writable($sDir))
			return false;
		// If target already exists, fail unless force
		if (!$bForce && file_exists($sTarget))
			return false;
		// Try to move
		if (!rename($this->m_sFile, $sTarget))
			return false;

		// Make sure file is still valid and points to new path
		$this->m_sFile = $sTarget;
		return true;
	}

	/**
	 * Alias for move method
	 */
	public function rename() {
		return call_user_func_array(array(get_class($this), 'move'), func_get_args());
	}
	
	/**
	 * Deletes a file on disk and does it's best to reset this instance of object
	 * @return bool       If was able to delete the file
	 */
	public function delete() {
		if (!unlink($this->m_sFile))
			return false;

		// Should destroy this object, instead always check still exists in methods
		$this->m_sFile = '';
		return true;
	}

	public function isImage() {
	}

	public function isDocument() {
	}

	public function isText() {
	}

	public function resizeImage() {
	}

	/**
	 * Determines the file type of a given file
	 * @return   mixed        false if unknown, string of type
	 */
	public function getType() {
	}

	/**
	 * Determines the file type of a given file
	 * @return   mixed                 false if unknown, string of type
	 */
	public function getMime() {
		// For images, exif_imagetype
		if ($this->isImage())
			;
		// Get mime via extension -- easy to spoof
		// Get mime via browser -- easy to spoof
		//TODO:
	}

	private function getImageMime() {
		//TODO:
	}

	/**
	 * Gets the size of this file in bytes
	 * @return   int   Size of file in bytes
	 */
	public function getSize() {
		return filesize($this->m_sFile);
	}

	/**
	 * Gets the last modified unix timestamp.
	 * @return   mixed      Unix timestamp when modified
	 */
	public function getLastModified() {
		return filemtime($this->m_sFile);
	}
	
	/**
	 * Gets the creation unix timestamp (in Linux, sort of).
	 * @return   mixed      Unix timestamp when created
	 */
	public function getCreated() {
		return filectime($this->m_sFile);
	}
	
	/**
	 * Returns the path to the directory containing this file
	 * @return   string         Directory where file stored
	 */
	public function getDirectory() {
		return pathinfo($this->m_sFile, PATHINFO_DIRNAME);
	}
	
	/**
	 * Returns the name of this file
	 * @return   string         Filename with extension
	 */
	public function getFilename() {
		return pathinfo($this->m_sFile, PATHINFO_FILENAME);
	}

	
}
?>
