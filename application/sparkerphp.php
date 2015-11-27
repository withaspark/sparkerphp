<?php if (!defined('INCLUDES_OK')) die ('Invalid SparkerPHP configuration.');
require_once('router.php');
require_once('inputs.php');
require_once('outputHelper.php');

class SparkerPHP
{
	public $inputs = null; // Pointer to inputs object

	private $m_sRoute = ''; // Route string
	private $m_sMethod = ''; // Method string
	private $m_sArgs = array(); // Array of arguments
	private $m_sRequires = array(); // Array of file paths that need to be included
	private $m_Messages = array(); // Array of messages to be displayed as (type, message) pairs where type is error, message, confirm

	private $m_Data = array(); // Data to display in view

	private $m_pRouter = null; // Pointer to router object
	private $m_pView = null; // Pointer to view object

	private static $m_instance = null; // Pointer to this instance



	private function __construct() {
		$this->m_iStartTime = microtime(true);
		$this->inputs = Inputs::getInstance();
	}

	public function __destruct() { }

	public static function getInstance() {
		if (!self::$m_instance)
			self::$m_instance = new SparkerPHP();
		return self::$m_instance;
	}

	/**
	 * Starts routing
	 */
	public function start($request) {
		$this->getRoute($request);
		$this->loadRouter();

		$this->addData('title', __APPTITLE__);
		$this->addData('approot', __APPPATH__);
		$this->addData('route', $this->m_sRoute);
		$this->addData('method', $this->m_sMethod);
	}

	/**
	 * Extracts the route/method[/param1/param2..] from the request.
	 *
	 * @param   $request   string   slash delimited route/method[/param1/param2..]
	 */
	private function getRoute($request) {
		$apppath = explode('/', $request);
		// Get router
		$this->m_sRoute = strtolower((isset($apppath[0]) && $apppath[0] != '') ? $apppath[0] : __DEFAULTROUTE__);
		// Get method
		$this->m_sMethod  = strtolower((isset($apppath[1]) && $apppath[1] != '') ? $apppath[1] : 'index');
		// Get arguments
		for ($i = 2; isset($apppath[$i]) && $apppath[$i] != ''; $i++) {
			$this->m_sArgs[]  = $apppath[$i];
		}
	}

	/**
	 * Instantiates router and calls the appropriate logic based on route
	 */
	private function loadRouter() {
		if ($this->addRequire('../routes/'.$this->m_sRoute.'.php')) {
			$this->includeRequired();
			$approute = ucwords($this->m_sRoute) . 'Router';
			$this->m_pRouter = new $approute();
			unset($approute);
			if ($this->m_pRouter->callMethod($this->m_sMethod, $this->m_sArgs)) {
				// Add all router data to app data
				foreach ($this->m_pRouter->getData() as $k => $v) {
					$this->addData($k, $v);
				}
			}
			else {
				if (__DEBUG__) $this->addMessage("No method ' $this->m_sMethod '.", 'error');
				$this->loadView('404');
			}
		}
		else {
			if (__DEBUG__) $this->addMessage("No route ' $this->m_sRoute '.", 'error');
			$this->loadView('404');
		}
	}

	/**
	 * Builds the application view object with header, footer, and view.
	 */
	public function loadView($page = null) {
		// List of HTTP error codes and text
		$sErrorPages = array(
			'400' => 'Bad Request',
			//'401' => 'Unauthorized', // Not using basic HTTP auth, use 403 instead
			'403' => 'Forbidden',
			'404' => 'Not Found',
			'503' => 'Service Unavailable',
		);

		// If called without a page, call router's view
		if ($page == null && $this->m_pRouter)
			$page = $this->m_pRouter->getView();
		else if ($page == null)
			$page = '404';

		// If view exists, render template and view
	        if ($this->addRequire('../views/header.php')
	         && $this->addRequire("../views/$page.php")
	         && $this->addRequire('../views/footer.php')) {
			// Automatically add appropriate headers if an error page
			if (array_key_exists($page, $sErrorPages)) {
				header('HTTP/1.0 '.intval($page).' '.$sErrorPages[$page]);
			}

			$this->m_pView = $this->parseView('../views/header.php', $this->getData());
			// Add all error and message boxes if in debug mode; if not, leave up to view
			if (__DEBUG__) {
				foreach ($this->getMessages() as $message) {
					$this->m_pView .= "\n".'<div class="'.$message[0].'_box"><b>'.ucwords($message[0]).'</b>: '.ucfirst($message[1]).'</div>';
				}
			}
			$this->m_pView .= "\n".$this->parseView("../views/$page.php", $this->getData())
					.$this->parseView('../views/footer.php', $this->getData());
	        }
	        else if ($page != '404') {
			if (__DEBUG__) $this->addMessage("No view ' $page '.", 'error');
	                $this->loadView('404');
	        }
	}

	/**
	 * Gets all substitutions for the page as array
	 *
	 * @return array
	 */
	public function getData() {
		return $this->m_Data;
	}

	/**
	 * Adds a key-value pair for use in view substitutions
	 *
	 * @param    $name   string   variable to substitute for in view
	 * @param    $value  mixed   what to replace the variable in the view file with
	 */
	public function addData($name, $value) {
		$this->m_Data[$name] = $value;
	}

	/**
	 * Gets all messages that need to be sent to the user.
	 *
	 * @return  array        Array of all array(type, message)
	 */
	public function getMessages() {
		return $this->m_Messages;
	}

	/**
	 * Adds an error or message type message to be displayed.
	 *
	 * @param   string   $message   Message to be displayed to user
	 * @param   string   $class     Type of message to be added: error, message, confirm
	 */
	public function addMessage($message, $class = 'message') {
		$this->m_Messages[] = array($class, $message);
	}

	/**
	 * Gets all file paths that must be included
	 *
	 * @return   array          Array of all required files to be included
	 */
	public function getRequires() {
		return $this->m_sRequires;
	}

	/**
	 * Adds a file paths that must be included
	 *
	 * @param   string   $requiredFile   File path that must be included
	 * @return  bool     $bExists        Does the required file exist
	 */
	public function addRequire($requiredFile) {
		$bExists = false;
		if (file_exists($requiredFile)) {
			$this->m_sRequires[] = $requiredFile;
			$bExists = true;
		}
		return $bExists;
	}

	/**
	 * Includes file paths that must be included and clears list of required files
	 */
	public function includeRequired() {
		foreach ($this->getRequires() as $reqd) {
			require_once($reqd);
		}
		$this->m_sRequires = array();
	}

	/**
	 * Make replacements for {$var}'s in view file
	 * 
	 * @param   $view_file       string   view file to parse
	 * @param   $substitutions   array    array of key-value pairs for substitution in views
	 * @return  string
	 */
	private function parseView($view_file, $substitutions) {
		// Go through all data and give current scope
		foreach ($substitutions as $k => $v) {
			global ${$k};
			${$k} = $v;
		}

		ob_start();
		require($view_file);
		$file_contents = ob_get_contents();
		ob_end_clean();

		// Replace all instances of {$variable} with variable's replacement
		foreach ($substitutions as $key => $sub) {
			// Arrays and objects will have to be within php tags
			if (!is_array($sub) && !is_object($sub))
				$file_contents = str_replace("{\$$key}", $sub, $file_contents);
		}

		// If programmer forgot to set replacement content in router, give error
		if (strpos($file_contents, '{$') !== false)
			$file_contents = preg_replace('/{\$(.*?)}/', $undefined_message = '<div class=\'error_box\'>Error: Content for {$$1} was not set in view for '.$this->m_sRoute.'/'.$this->m_sMethod.'. You must call <code>$this->addData(\'$1\', \'YOUR VALUE\');</code>somewhere in your router.</div>', $file_contents);

		return $file_contents;
	}

	/**
	 * Pieces together all view and returns a fully constructed view.
	 *
	 * @return   string    $this->m_pView   Fully constructed view ready for output to client
	 */
	public function render() {
		$this->loadView();
		return $this->m_pView;
	}
}
?>
