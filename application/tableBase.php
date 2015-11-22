<?php
require_once('database.php');



/**
 * Class that defines basic operations for simple ORM tasks when a class
 * reflects an existing database table. Stupid simple...use at own risk. To use
 */
class TableBase {
	protected $m_sTableName = ''; //!< Name of this table
	protected $m_sFields = array(); //!< List of fields in table to auto load and save
	
	public function __construct() {
		$this->m_sTableName = get_class($this);
	}

	/**
	 * Gets the records requested
	 * @param   string  $sWhere   Comma-separated list of where conditions
	 * @return  mixed             Array of key value pairs
	 */
	public function get($sWhere = '1=1') {
		$db = new DB();
		return $db->select('SELECT * FROM '.$this->getTableName().' WHERE :where', array(':where'=>$sWhere));
	}

	/**
	 * Gets a single record (the first)
	 * @param   string  $sWhere   Comma-separated list of where conditions
	 * @return  mixed             Key value pairs
	 */
	public function getOne($sWhere = '1=1') {
		$res = $this->get($sWhere);
		if (count($res) == 0)
			return array();
		return $res[0];
	}

	/**
	 * Populates an object with the values given in the key value pair.
	 * Note: Can easily take values directly from ::get method.
	 * @param   mixed   $Values   Array of key-value pairs to load into object
	 * @return  bool              If any sets failed
	 */
	public function build($Values) {
		$bFail = false;
		foreach ($Values as $sFieldName => $sFieldValue) {
			$sProperty = $sFieldName;
			if (property_exists($this->getTableName(), $sProperty))
				$this->$sProperty = $sFieldValue;
			else
				$bFail = true;

		}
		return $bFail;
	}

	/**
	 * Saves the key value pairs into the database. If $Fields is array of key value pairs, the
	 * given values will be saved. If $Fields is a vector of field names, the current values of
	 * this object will be saved for those fields. If $Fields isn't specified, the current
	 * values of this object will be saved for the fields setup for autosave in this class.
	 * @param  mixed  $Fields   Key-value pairs of data to push to table or array of fields to save from current object
	 * @return int              If saved successfully, <0: error, 0: no rows updated, >0: success
	 */
	public function save($Fields = array()) {
		$sParams = array(); //!< List of key value pairs to pass to the update
		$sQuery = ''; //!< Used to build the query
		$bIsListOfFields = false; //!< Is $Fields a list of fields

		// Either must give values, list of fields, or have auto save configured
		if (count($Fields) == 0 && count($this->m_sFields) == 0)
			return -1;

		// Only willing to assume list of fields if all keys are integers
		if (count($Fields) == 0) {
			$bIsListOfFields = true;
			foreach ($Fields as $k => $v) {
				if (!is_int($k)) {
					$bIsListOfFields = false;
					break;
				}
			}
		}

		// If no key-value pairs given or was given list of fields to update, use current property values
		if (count($Fields) == 0 || $bIsListOfFields) {
			// If auto save fields, add those
			if (!$bIsListOfFields && count($this->m_sFields) > 0) {
				$Fields = $this->m_sFields;
			}
			foreach ($Fields as $sField => $sValue) {
				$Fields[$sField] = $this->$sField;
			}
		}

		// Loop through values
		foreach ($Fields as $sKey => $sValue) {
			// Skip invalid keys, must start with letter or _, then alphanum or _ or . or -
			if (!preg_match('/^[a-zA-Z\_][a-zA-Z0-9\_\-\.]*/', $sKey))
				continue;

			if ($sQuery != '')
				$sQuery .= ', ';

			$sQuery .= "`$sKey`=:$sKey";
			$sParams[":$sKey"] = $sValue;
		}

		// Append update to query
		$sQuery = 'UPDATE '. self::getTableName() . ' SET ' . $sQuery;

		$db = new DB(); // Database object
		return $db->update($sQuery, $sParams);
	}

	/**
	 * Returns the name of the current table
	 * @return  mixed      Name of this table, false if unset
	 */
	public function getTableName() {
		return $this->m_sTableName;
	}
}
?>
