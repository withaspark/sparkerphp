<?php if (!defined('INCLUDES_OK')) die ('Invalid SparkerPHP configuration.');

class outputHelper
{
	/**
	 * Formats phone numbers
	 * 
	 * @access   private
	 * @param    integer   $phone   Phone number with no formatting
	 * @param    string    $format  Phone number format with x for each digit
	 * @return   string    $phone   Properly formatted phone number
	 */
	public static function formatPhone($phone, $format = '(xxx) xxx-xxxx') {
		if (is_numeric($phone) && $phone != 0) {
			$phoneArr = str_split($phone);
			$phone    = $format;

			// Step through replacing each x in format with the next number in phone number
			foreach ($phoneArr as $char)
				$phone = preg_replace('/x/', $char, $phone, 1);
		}
		else
			$phone = 'Unknown';

		return $phone;
	}



	/**
	 * Converts timestamps to readable date times
	 * 
	 * @param    integer   $date         Unix timestamp
	 * @param    string    $dateFormat   Date format string
	 * @param    string    $timeFormat   Time format string
	 * @param    boolean   $readable     Whether to show today's date as "Today"
	 * @return   string                  Readable datetime
	 */
	public static function formatDateTime($date, $dateFormat = '', $timeFormat = '', $readable = true) {
		return self::formatDate($date, $dateFormat, $readable) . ' at ' . self::formatTime($date, $timeFormat);
	}



	/**
	 * Converts timestamps to readable dates
	 * 
	 * @param    integer   $date         Unix timestamp
	 * @param    string    $dateFormat   Date format string
	 * @param    boolean   $readable     Whether to show today's date as "Today"
	 * @return   string    $date         Readable date
	 */
	public static function formatDate($date, $dateFormat = '', $readable = true) {
		if ($date != 0) {
			if (date('F j Y') == date('F j Y', $date) && $readable)
				// If today, say Today
				$date = 'Today';
			else
				$date = date($dateFormat, $date);
		}
		else
			$date = 'Unknown';
		return $date;
	}



	/**
	 * Converts date in form of YYYY/MM/DD to readable date
	 * 
	 * @param    string   $date         YYYY/MM/DD formated datestamp
	 * @param    string   $dateFormat   Date format string
	 * @return   string                 Readable date
	 */
	public static function formatDateStamp($date, $dateFormat) {
		return self::formatDate(self::formatUTime($date), $dateFormat);
	}



	/**
	 * Converts YYYY/MM/DD dates into unix timestamps
	 * 
	 * @param    string    $date   YYYY/MM/DD formated string date
	 * @param    string    $time   HH:MM:SS formated string time
	 * @access   public
	 * @return   integer           Unix timestamp date
	 */
	public static function formatUTime($date, $time = null) {
		$hour   = 0;
		$minute = 0;
		$second = 0;
		$month  = 0;
		$day    = 0;
		$year   = 0;

		if (strpos($date, '/') !== false) {
			$date  = explode('/', $date);
			$year  = $date[0];
			$month = $date[1];
			$day   = $date[2];
		}
		if (strpos($time,':') !== false) {
			$time   = explode(':', $time);
			$hour   = $time[0];
			$minute = $time[1];
			$second = $time[2];
		}

		$date = mktime($hour, $minute, $second, $month, $day, $year);
		$date = ($date != 943938000) ? $date : 0;

		// Note: If date of all 0s, mktime returns 943938000; don't know why
		return $date;
	}



	/**
	 * Returns YYYY/MM/DD formated date from unix timestamp
	 * 
	 * @param    integer   $date   Unix timestamp
	 * @access   public
	 * @return   string            YYYY/MM/DD formated date
	 */
	public static function formatDatepicker($date) {
		return date('Y/m/d', $date);
	}



	/**
	 * Converts timestamps to readable times
	 * 
	 * @param    integer   $time         Unix timestamp
	 * @param    string    $timeFormat   Time format string
	 * @return   string    $time         Readable time
	 */
	public static function formatTime($time, $timeFormat) {
		if ($time != 0) {
			$time = date($timeFormat, $time);
		}
		else
			$time = 'Unknown';

		return $time;
	}



	/**
	 * Converts money amounts
	 * 
	 * @param    float    $amount        Amount
	 * @param    string   $moneyFormat   Time format string
	 * @return   string                  Readable money amount
	 */
	public static function formatMoney($amount, $moneyFormat = '$x USD') {
		$negative = false;
		// This allows you to send input boxes as $amount and it will surround it with proper formatting
		if (is_numeric($amount)) {
			if ($amount < 0) {
				$negative = true;
				$amount   = $amount * -1;
			}

			$amount = number_format($amount, 2);
		}
		elseif ($amount == '')
			$amount = number_format(0,2);

		return ($negative) ? '-' . str_replace('x', $amount, $moneyFormat) : str_replace('x', $amount, $moneyFormat);
	}



	/**
	 * Turns text web addresses into links
	 * 
	 * @param    string    $text    text to convert links in
	 * @access   public
	 * @return   string    $text
	 */
	public static function make_urls_links($text) {
		// Turns [protocol]://[address] strings into links, but not images, links, etc.
		// Notice: target=_blank, opens links in new window/tab
		if (strpos($text,'script') == 0)
			if (preg_match("/[\"|'][[:alpha:]]+:\/\//",$text) == false)
				$text = preg_replace('/([[:alpha:]]+:\/\/[^<>[:space:]]+[[:alnum:]\/])/', '<a href="\\1" target="_blank">\\1</a>', $text);

		return $text;
	}



	/**
	 * Replaces newline line breaks with html line breaks
	 * 
	 * @param    string    $text         Text to operate on
	 * @access   public
	 * @return   string    $text         Text after modifications
	 */
	public static function linebreaks($text) {
		$text = str_replace('\n','<br />',$text);
		$text = str_replace("\n",'<br />',$text);

		return $text;
	}



	/**
	 * Formats text strings by adding html with css
	 * 
	 * @param    string   $text   Text to format
	 * @access   public
	 * @return   string   $text   Text with added html tags
	 */
	public static function textFormatting($text) {
		// Bold text by surrounding with **
		$text = preg_replace('/\*\*(.*)\*\*/U', '<span style="font-weight: bold;">\\1</span>', $text);
		// Underline text by surrounding with __ (double underscores)
		$text = preg_replace('/__(.*)__/U', '<span style="text-decoration: underline;">\\1</span>', $text);
		// Italicize text by surrounding with ~~
		$text = preg_replace('/~~(.*)~~/U', '<span style="font-style: italic;">\\1</span>', $text);
		// Increase font size to xx-large by surrounding with ++++
		$text = preg_replace('/\+\+\+\+(.*)\+\+\+\+/U', '<span style="font-size: xx-large;">\\1</span>', $text);
		// Increase font size to x-large by surrounding with ++
		$text = preg_replace('/\+\+(.*)\+\+/U', '<span style="font-size: x-large;">\\1</span>', $text);	

		return $text;
	}



	/**
	 * Combines most common operations for blocks of text
	 * 
	 * @param    string   $text   Text to operate on
	 * @access   public
	 * @return   string   $text   Text after modifications
	 */
	public static function displayBlockText($text) {
		$text = self::linebreaks($text);
		$text = self::textFormatting($text);

		// make_urls_links must follow self::textFormatting or
		// '//' after protocol will be interpretted as italic signaler
		$text = self::make_urls_links($text); 

		if ($text == '') $text = 'Unknown';

		return $text;
	}



	/**
	 * Returns html required to build pagination links
	 * 
	 * @param    string    $base_url    Page where results are being displayed
	 * @param    integer   $records     Sizeof records array
	 * @param    integer   $offset      Number of results to skip
	 * @param    integer   $limit       Number of results to show
	 * @param    string    $getParams   Get parameters to retain with pagination
	 * @access   public
	 * @return   string    $html        HTML to build pagination links
	 */
	public static function pagination($base_url, $records, $offset, $limit, $getParams = false) {
		// Allows passing of get parameters
		$getParams = ($getParams) ? '?' . $getParams : '';
		
		$html = '<p class=\'right\'>';
		if ($offset != 0)                       $html .= "<a href='$base_url/" . ($offset - $limit) . '/' . $limit . $getParams . '\'>Previous</a>';
		if ($offset != 0 && $records == $limit) $html .= ' | ';
		if ($records == $limit)                 $html .= "<a href='$base_url/" . ($offset + $limit) . '/' . $limit . $getParams . '\'>Next</a>';
		$html .= '</p>';

		return $html;
	}



	/**
	 * Converts sizes given in bytes to a human readable size always rounding up.
	 * @param   integer   $iSize        Size in bytes
	 * @param   integer   $iPrecision   Precision of output--number of decimal places, assumed positive
	 * @return  string                  Human readable size
	 */
	public static function convertBytesToHuman($iSize, $iPrecision = 2) {
		$sPrefixes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$iFactor   = 1;
		$sUnits    = 'B';
		$iMult     = pow(10, abs($iPrecision));

		for ($ii = sizeof($sPrefixes) - 1; $ii >= 0; $ii--) {
			if ($sPrefixes[$ii] != '' && abs($iSize) > pow(1000, $ii)) {
				$iFactor = pow(1000, $ii);
				$sUnits  = $sPrefixes[$ii];
				break;
			}
		}

		// Always rounds up and contains $iPrecision number of decimal places
		return sprintf("%01.{$iPrecision}f", (ceil(($iSize/$iFactor) * $iMult) / $iMult)) . $sUnits;
	}



	/**
	 * Converts sizes given in human readable size to bytes
	 * @param   string   $sSize   Human readable size
	 * @return  mixed             Size in bytes or false if cannot convert
	 */
	public static function convertHumanSizeToBytes($sSize) {
		$sUnits  = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$fValue  = floatval($sSize);
		$sUnit   = trim(str_replace($fValue, '', $sSize));
		$iFactor = array_search($sUnit, $sUnits);
		if ($iFactor === false)
			return false;

		return $fValue * pow(1000, $iFactor);
	}



	/**
	 * Generates a select input for choosing timezone
	 * 
	 * @param    string   $selectName   Name for select
	 * @access   public
	 * @return   string   $html         HTML for select box
	 */
	public static function timezoneSelect($selectName) {
		$timezones = array(
			'America/New_York'    => 'EDT (Eastern/New York)',
			'America/Chicago'     => 'CDT (Central/Chicago)',
			'America/Boise'       => 'MDT (Mountain/Boise)',
			'America/Phoenix'     => 'MST (Arizona/Pheonix)',
			'America/Los_Angeles' => 'PDT (Pacific/Los Angeles)',
			'America/Juneau'      => 'AKDT (Alaska/Juneau)',
			'Pacific/Honolulu'    => 'HST (Hawaii/Honolulu)',
			'America/Puerto_Rico' => 'AST (Atlantic/Puerto Rico)',
			'Pacific/Guam'        => 'ChST (Chamorro/Guam)',
			'Pacific/Samoa'       => 'SST (Swedish Summer/Samoa)',
			'Pacific/Wake'        => 'WAKT (Wake Time/Wake)',
		);

		$html = "<select name='$selectName' id='$selectName'>";
		foreach ($timezones as $key => $row) {
			$html .= "<option value='$key'>$row</option>";
		}
		$html .= '</select>';

		return $html;
	}
}
?>
