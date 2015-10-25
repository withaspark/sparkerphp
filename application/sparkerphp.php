<?php
require_once('router.php');

class SparkerPHP
{
	private $m_sRoute = ''; // Route string
	private $m_sMethod = ''; // Method string
	private $m_sArgs = array(); // Array of arguments
	private $m_sRequires = array(); // Array of file paths that need to be included
	private $m_sMessages = array(); // Array of messages to be displayed (errors, warnings, etc.)

	private $m_Params = array(); // Array of $_GET, $_POST
	private $m_Data = array(); // Data to display in view

	private $m_pRouter = null; // Pointer to router object
	private $m_pView = null; // Pointer to view object


	public function __construct($request) {
		$this->m_iStartTime = microtime(true);
		$this->getRoute($request);
		$this->processParams();
		$this->loadRouter();
	}

	public function __destruct() { }

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
			if ($this->m_pRouter->callMethod($this->m_sMethod, $this->m_sArgs, $this->m_Params)) {
				// Add all router data to app data
				foreach ($this->m_pRouter->getData() as $k => $v) {
					$this->addData($k, $v);
				}
			}
			else {
				if (__DEBUG__) $this->addMessage("No method ' $this->m_sMethod '.", 'error');
				$this->load404();
			}
		}
		else {
			if (__DEBUG__) $this->addMessage("No route ' $this->m_sRoute '.", 'error');
			$this->load404();
		}
	}

	/**
	 * Loads all sanitized $_GET and $_POST parameters into the application object and makes
	 * global $_GET and $_POST unavailable so don't accidentally use dirty value.
	 */
	private function processParams() {
		// Note: this stomps key, keep gets before posts
		foreach ($_GET as $k => $v) {
			//TODO: add validation here where name of input is queried against the sanitization
			// routine lookup and only adding if valid
			$this->m_Params[$k] = $v;
		}
		foreach ($_POST as $k => $v) {
			//TODO: add validation here where name of input is queried against the sanitization
			// routine lookup and only adding if valid
			$this->m_Params[$k] = $v;
		}
		// Unset $_GET and $_POST so only used sanitized values
		unset($_GET);
		unset($_POST);
	}

	/**
	 * Builds the application view object with header, footer, and view.
	 */
	public function loadView($page = null) {
		// If called without a page, call router's view
		if ($page === null)
			$page = $this->m_pRouter->getView();

		// If view exists, render template and view
	        if ($this->addRequire('../views/header.php')
	         && $this->addRequire("../views/$page.php")
	         && $this->addRequire('../views/footer.php')) {
			$this->m_pView = $this->parseView('../views/header.php', $this->getData());
			// Add all error and message boxes
			foreach ($this->getMessages() as $message) {
				$this->m_pView .= "\n".'<div class="'.$message[0].'_box">'.ucwords($message[0]).': '.$message[1].'</div>';
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
	 * @return  array
	 */
	public function getMessages() {
		return $this->m_sMessages;
	}

	/**
	 * Adds an error or feedback message to be displayed.
	 */
	public function addMessage($message, $class = 'feedback') {
		$this->m_sMessages[] = array($class, $message);
	}

	public function getRequires() {
		return $this->m_sRequires;
	}

	public function addRequire($requiredFile) {
		$bExists = false;
		if (file_exists($requiredFile)) {
			$this->m_sRequires[] = $requiredFile;
			$bExists = true;
		}
		return $bExists;
	}

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
				$file_contents = preg_replace('/{\$'.$key.'}/', $sub, $file_contents);
		}

		// If programmer forgot to set replacement content in router, give error
		$file_contents = preg_replace('/{\$(.*?)}/', $undefined_message = '<div class=\'error_box\'>Error: Content for {$$1} was not set in view for '.$this->m_sRoute.'/'.$this->m_sMethod.'. You must call <code>$this->addData(\'$$1\', \'YOUR VALUE\');</code>somewhere in your router.</div>', $file_contents);

		return $file_contents;
	}

	public function render() {
		$this->loadView();
		return $this->m_pView;
	}

	/**
	 * Load 404 page not found
	 */
	public function load404() {
		header('HTTP/1.0 404 Not Found');
                $this->loadView('404');
	}
}
?>
