<?php
require_once('modules/xss.php');

class Schema
{
	private $value        = null; // Value of data to clean
	private $foreign      = null; // Link to foreign key so don't duplicate definitions
	private $filter       = null; // What to strip; if what to leave, must set 'negative' => true
	private $negative     = null; // If strip regex instead of allow regex
	private $description  = null; // Short note on the format of the expected data
	private $min_length   = null; // Minimum number of characters accepted
	private $max_length   = null; // maximum number of characters accepted
	private $tables       = array(); // Array of schema configuration data

	private static $m_instance = null; // Pointer to this instance of schema class


	private function __construct() {
		$this->readSchema();
	}
	public function __destruct() {
		$this->m_instance = null;
	}
	public static function getInstance() {
		if (self::$m_instance === null) {
			self::$m_instance = new Schema();
		}
		return self::$m_instance;
	}

	/**
	 * Returns the schema information for a given table and field
	 *
	 * @param    $table   string   table to use as model for type
	 * @param    $field   string   field to use
	 * @return   $schema  array    schema for requested element
	 */
	public function getSchema($table = null, $field = null) {
		$schema = array();

		// If provided table and field, return field schema
		if ($field !== null && $table !== null
		    && array_key_exists($table, $this->tables)
		    && array_key_exists($field, $this->tables['field'])
		) {
			$schema = $this->tables[$table][$field];
		}
		// If provided table but not field, return entire table schema
		else if ($field === null && $table !== null && array_key_exists($table, $this->tables)) {
			$schema = $this->tables[$table];
		}
		// Else, give all schema
		else {
			$schema = $this->tables;
		}

		return $schema;
	}





	/**
	 * Reads the schema json file and loads into tables variable
	 */
	private function readSchema() {
		$sSchemaFile = '../config/schema.json';
		if (file_exists($sSchemaFile)) {
			$this->tables = json_decode(file_get_contents($sSchemaFile), TRUE);
		}
		$this->tables['generic'] = array(
			'text' => array(
				'filter'      => '/<*?[^<>]*?>/', // Since didn't make negative=true, will filter but won't give error
				'description' => 'no html entities',
				'min_length'  => 0,
				'max_length'  => 10000,
				'input' => array(
					'type' => 'textarea',
				),
			),
			'html' => array(
				'filter'      => '/<script*?[^<>]*?>/',
				'negative'    => false,
				'description' => 'no scripting allowed',
				'min_length'  => 0,
				'max_length'  => 10000,
				'input' => array(
					'type' => 'textarea',
				),
			),
			'integer' => array(
				'filter'      => '/[^\d]/',
				'description' => 'integers only',
				'min_length'  => 1,
				'max_length'  => 100,
				'input' => array(
					'type' => 'text',
				),
			),
			'boolean' => array(
				'filter'      => '/([01])/',
				'negative'    => true,
				'description' => '0 or 1 only',
				'min_length'  => 1,
				'max_length'  => 1,
				'input' => array(
					'type' => 'checkbox',
				),
			),
			'money' => array(
				'filter'      => '/^([\-]{0,1})[0-9]+(\.[0-9]{0,2})?$/',
				'negative'    => true,
				'description' => 'decimal numbers only',
				'min_length'  => 1,
				'max_length'  => 10,
				'input' => array(
					'type' => 'text',
				),
			),
			'decimal' => array(
				'filter'      => '/^([\-]{0,1})[0-9]+(\.[0-9])?$/',
				'negative'    => true,
				'description' => 'decimal numbers only',
				'min_length'  => 1,
				'max_length'  => 10,
				'input' => array(
					'type' => 'text',
				),
			),
			'alphanumeric' => array(
				'filter'      => '/[^a-zA-Z0-9]/',
				'description' => 'letters and numbers only',
				'min_length'  => 0,
				'max_length'  => 10000,
				'input' => array(
					'type' => 'text',
				),
			),
			'extended' => array(
				'filter'      => '/[^a-zA-Z0-9_\-\.]/',
				'description' => 'letters, numbers, and _-. only',
				'min_length'  => 0,
				'max_length'  => 10000,
				'input' => array(
					'type' => 'text',
				),
			),
			'alpha' => array(
				'filter'      => '/[^a-zA-Z]/',
				'description' => 'letters only',
				'min_length'  => 0,
				'max_length'  => 10000,
				'input' => array(
					'type' => 'text',
				),
			),
			'date' => array(
				'filter'      => '/([0-9]{4}\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01]))/',
				'negative'    => true,
				'description' => 'YYYY/MM/DD',
				'min_length'  => 10,
				'max_length'  => 10,
				'input' => array(
					'label' => 'Date',
					'type'  => 'text',
				),
			),
			'name' => array(
				'filter'      => '/[^a-zA-Z0-9\s_\-\.\,\!]/',
				'description' => 'letters, numbers, whitespace, and _-.,! only',
				'min_length'  => 2,
				'max_length'  => 50,
				'input' => array(
					'label' => 'Title',
					'type'  => 'text',
				),
			),
			'email' => array(
				'filter'      => '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,25}){0,1}$/', // Allows emails to be blank as long as min_length is overridden
				'negative'    => true,
				'description' => 'email@example.com',
				'min_length'  => 8,
				'max_length'  => 50,
				'input' => array(
					'label' => 'Email address',
					'type'  => 'text',
				),
			),
			'telephone' => array(
				'filter'      => '/[^0-9]/',
				'description' => 'phone number, numbers only, no dashes or spaces',
				'min_length'  => 0,
				'max_length'  => 20,
				'input' => array(
					'label' => 'Telephone number',
					'type'  => 'text',
				),
			),
			'ip' => array(
				'filter'      => '/(\d{1,3}\.){3}\d{1,3}/',
				'negative'    => true,
				'description' => 'xxx.xxx.xxx.xxx',
				'min_length'  => 7,
				'max_length'  => 15,
				'input' => array(
					'label' => 'IP address',
					'type'  => 'text',
				),
			),
			'url' => array(
				'filter'      => '/[^a-zA-Z0-9_:\?\=\&\@\[\]\/\.\-\ ]/i',
				'description' => 'valid url, e.g. http://www.example.com/page.html',
				'min_length'  => 0,
				'max_length'  => 500,
				'input' => array(
					'label' => 'URL',
					'type'  => 'text',
				),
			),
			'utime' => array(
				'filter'      => '/[^\d]/',
				'description' => 'integer unix timestamp',
				'min_length'  => 1,
				'max_length'  => 10,
				'input' => array(
					'label' => 'Unix timestamp',
					'type'  => 'text',
				),
			),
			'counter' => array(
				'filter'      => '/[^\d]/',
				'description' => 'integer',
				'min_length'  => 0,
				'max_length'  => 10,
				'input' => array(
					'type' => 'text',
				),
			),
			'hex' => array(
				'filter'      => '/[^a-f0-9]/',
				'description' => 'hexadecimal string',
				'min_length'  => 0,
				'max_length'  => 50,
				'input' => array(
					'type' => 'text',
				),
			),
			'timezone' => array(
				'filter'      => '/([a-zA-Z0-9]*)\/([a-zA-Z0-9\_]*)/',
				'negative'    => true,
				'description' => 'timezone identifier',
				'min_length'  => 10,
				'max_length'  => 50,
				'input' => array(
					'label'   => 'Timezone',
					'type'    => 'select',
					'options' => array(
						'America/New_York'    => 'EDT (Eastern/New York)',
						'America/Chicago'     => 'CDT (Central/Chicago)',
						'America/Boise'	      => 'MDT (Mountain/Boise)',
						'America/Phoenix'     => 'MST (Arizona/Pheonix)',
						'America/Los_Angeles' => 'PDT (Pacific/Los Angeles)',
						'America/Juneau'      => 'AKDT (Alaska/Juneau)',
						'Pacific/Honolulu'    => 'HST (Hawaii/Honolulu)',
						'Pacific/Guam'        => 'ChST (Chamorro/Guam)',
						'Pacific/Samoa'       => 'SST (Swedish Summer/Samoa)',
						'Pacific/Wake'        => 'WAKT (Wake Time/Wake)',
					),
				),
			),
			'tag' => array(
				'filter'      => '/[^0-9a-f]/',
				'description' => 'ID',
				'min_length'  => 0,
				'max_length'  => 30,
				'input' => array(
					'label' => 'Tags',
					'type'  => 'text',
				),
			),
			'color' => array(
				'filter'      => '/([0-9a-f]{3}){1,2}/',
				'negative'    => true,
				'description' => 'rgb hex color',
				'min_length'  => 0,
				'max_length'  => 6,
				'input' => array(
					'label' => 'RGB color',
					'type'  => 'text',
				),
			),
		);
	}


	/**
	 * Cleans an element of a table
	 *
	 * @param    $value   string   value to clean
	 * @param    $table   string   table to use as model for type
	 * @param    $field   string   field to use as model for type
	 * @access   public
	 * @return   array(
	 *              boolean 'clean'   Whether $value was acceptable
	 *              string  'value'   The cleaned up value
	 *              string  'error'   The reason $value was considered unclean
	 *           )
	 */
	public function clean_by_element($value, $type) {
		$success = false;
		$error   = null;
		$found   = false;    // If have found a matching element
		$limits  = array();  // Array of filtering rules

		$limits  = $this->getParams($type);
		list($success, $value, $error) = $this->__clean_type((string)$value, $type, $limits);

		return array(
			'clean' => (int)$success,
			'value' => $value,
			'error' => $error
		);
	}


	/**
	 * Gets the validation parameters for an element
	 *
	 * @access    public
	 * @param     string   $field   if table field sent as table--field, else considered type
	 * @return    array             array of validation parameters
	 */
	public function getParams($field) {
		$arr         = null; // Used to determine the variable name to look in
		$key         = null; // Key of $arr[$key] containing filtering rules
		$innerkey    = null; // Individual key used for comparing to validation parameters
		$innerparam  = null; // Individual parameter used for comparing to validation parameters
		$foreign     = null; // Reference to another data element in schema
		$filter      = null; // Regex of filter
		$negative    = null; // If contains what to strip instead of allow
		$description = null; // Error message when fails filter
		$min_length  = null; // Minimum length of value
		$max_length  = null; // Maximum length of value

		/**
		 *  Use field to determine what $tables[$table][$key] to look in
		 *  	$field as variable--key => $tables[$table][$key]
		 *  	$field as type => $tables['generic'][$key]
		 */
		// If sent a reference to table--field value
		if (strpos($field, '--') >= 1) {
			list($arr, $key) = explode('--', $field);
		}
		// If given a generic type
		else {
			$arr = 'generic';
			$key = $field;
		}

		// Set parameters but do NOT overwrite, higher level parameters always take precedence
		if (array_key_exists($arr, $this->tables) && array_key_exists($key, $this->tables[$arr])) {
			if(array_key_exists('filter', $this->tables[$arr][$key]) && $filter == null)
				$filter      = $this->tables[$arr][$key]['filter'];
			if(array_key_exists('negative', $this->tables[$arr][$key]) && $negative == null)
				$negative    = $this->tables[$arr][$key]['negative'];
			if(array_key_exists('description', $this->tables[$arr][$key]) && $description == null)
				$description = $this->tables[$arr][$key]['description'];
			if(array_key_exists('max_length', $this->tables[$arr][$key]) && $max_length == null)
				$max_length  = $this->tables[$arr][$key]['max_length'];
			if(array_key_exists('min_length', $this->tables[$arr][$key]) && $min_length == null)
				$min_length  = $this->tables[$arr][$key]['min_length'];
			if(array_key_exists('foreign', $this->tables[$arr][$key])) {
				$foreign     = $this->tables[$arr][$key]['foreign'];

				foreach ($this->getParams($foreign) as $innerkey => $innerparam) {
					if($$innerkey === null)
						$$innerkey = $innerparam;
				}
			}
		}

		return array(
			'foreign'     => $foreign,
			'filter'      => $filter,
			'negative'    => $negative,
			'description' => $description,
			'min_length'  => $min_length,
			'max_length'  => $max_length,
		);
	}



	/**
	 * Cleans and trims the value sent, tells whether valid, and what errors were encountered (if any)
	 *
	 * @param    $limits      array     validation criteria to use for $this->value
	 * @param    $type        string    type of value
	 * @param    $min_length  integer   minimum length of $value if different from default in schema
	 * @param    $max_length  integer   maximum length of $value if different from default in schema
	 * @access   private
	 * @return   array(
	 *              boolean   'clean'   Whether $value was acceptable
	 *              string    'value'   The cleaned up value
	 *              string    'error'   The reason $value was considered unclean
	 *           )
	 */
	private function __clean_type($value, $type, $limits, $min_length = null, $max_length = null) {
		if (strpos($type, '--')) {
			$type = explode('--', $type);
			$type = $type[1];
		}

		$type = str_replace('_', ' ', $type);

		// Defaults if no filter is given for data
		$success     = false;
		$error       = null;
		$filter      = null;
		$description = null;
		$temp_value  = $value; // Holds copy of $value; used when $negative is set and needs to invert results

		if (array_key_exists('filter', $limits))
			$filter = $limits['filter'];
		if (array_key_exists('description', $limits))
			$description = $limits['description'];

		// Filter value
		if ($filter !== null) {
			// Defaults if filtering is enabled
			$success = false;
			$error   = $type.' should be of the form: '.$description;

			// Check if invalid
			if (preg_match($filter, $value)) {
				$success = false;
				$error   = $type.' should be of the form: '.$description;
				$value   = preg_replace($filter, '', $value);

				// If filter is matched and value replace, but negative key is set
				if ($value == '' && array_key_exists('negative', $limits)) {
					// And negative key is true
					// A good value was cleared, so reset it
					if ($limits['negative'] === true) {
						$success = true;
						$value   = $temp_value;
						$error   = null;
					}
				}
			}
			// If no match found
			else {
				$success = true;
				$value   = $temp_value;
				$error   = null;

				// But if negative, opposite is true
				if (array_key_exists('negative', $limits)) {
					if ($limits['negative'] === true) {
						$success = false;
						$error   = $type.' should be of the form: '.$description;
						$value   = preg_replace($filter, '', $value);
					}
				}
			}
		}

		// If value has been wiped out by replace, set success to false
		if ($value == '' && $limits['min_length'] > 0)
			$success = false;

		if (array_key_exists('min_length', $limits)) $min_length = $limits['min_length'];
		if (array_key_exists('max_length', $limits)) $max_length = $limits['max_length'];

		// Get lengths from function call if sent and overwrite
		if ($min_length !== null) $min_length = $min_length;
		if ($max_length !== null) $max_length = $max_length;

		// Trim to max_length
		if ($max_length) {
			if (strlen($value) > $max_length) {
				$success = false;
				if ($error == null) {
					$error = $type.' should be ';
					$error .= ($min_length != $max_length) ? 'less than ' : '';
					$error .= $max_length.' characters long';
				}
			}

			$value = substr($value, 0, $max_length);
		}

		// Check min_length
		if ($min_length) {
			if (strlen($value) < $min_length) {
				$success = false;
				if ($error == null) {
					$error = $type.' should be ';
					$error .= ($min_length != $max_length) ? 'at least ' : '';
					$error .= $min_length.' characters long';
				}
			}
		}

		// Basic cleaning to perform on all values
		$value = addslashes($value);
		$value = XSSCleaner::clean($value);

//		if ($type != 'html') {
//			$value = htmlentities($value);
//			//$value = filter_var($value, FILTER_SANITIZE_STRIPPED);
//		}
//
//		if ($type == 'email')
//			$value = filter_var($value, FILTER_SANITIZE_EMAIL);
//		elseif ($type == 'integer'
//			|| $type == 'boolean'
//			|| $type == 'telephone'
//			|| $type == 'utime'
//			|| $type == 'counter'
//		)
//			$value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
//		elseif ($type == 'decimal'
//			|| $type == 'money'
//		)
//			$value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
//		elseif ($type == 'url')
//			$value = filter_var($value, FILTER_SANITIZE_URL); // Note: Strips spaces which are used to display feedback after redirect

		return array($success, $value, $error);
	}
}
?>
