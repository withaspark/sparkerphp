<?php
require_once('inputs.php');

class Router
{
	protected $inputs = null; // Pointer to inputs instance

	private $m_sRoute = ''; // Holds the route name
	private $m_sMethod = ''; // Method called
	private $m_sView = ''; // Path of view to render
	private $m_Messages = array(); // Array of messages to be displayed as (type, message) pairs where type is error, message
	private $m_Data = array(); // Array of key-value substitutions used to push data into views


	public function __construct() {
		// Route is difference between this class and parent class names
		$this->m_sRoute = strtolower(str_replace(get_parent_class($this), '', get_class($this)));
		$this->inputs = Inputs::getInstance();
	}

	/**
	 * Actions to perform before running controller method
	 *
	 * @return  void
	 */
	protected function preload() { }
	
	/**
	 * Actions to perform after running controller method
	 */
	protected function postload() {
		// Add all messages to the view
		$messages = array('error' => array(), 'message' => array());
		$msgs = $this->getMessages();
		foreach ($msgs as $msg) {
			if ($msg[0] == 'error' || $msg[0] == 'message')
				$messages[$msg[0]][] = $msg[1];
		}
		$this->addData('errorMessages', $messages['error']);
		$this->addData('messageMessages', $messages['message']);
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
	 * @param    $value  mixed    what to replace the variable in the view file with
	 * @return   void
	 */
	protected function addData($name, $value) {
		$this->m_Data[$name] = $value;
	}

	/**
	 * Calls the preload, method, and postload functions.
	 *
	 * @param   $method   string   method to call
	 * @param   $args     array    arguments to pass to method
	 * @return  bool
	 */
	public function callMethod($method, $args) {
		$methodExists = false;

		if (method_exists($this, $method)) {
			$methodExists = true;
			$this->m_sMethod = $method;

			$this->setView($this->m_sRoute.'/'.$this->m_sMethod);
			$this->preload();
			$this->$method($args);
			$this->postload();
		}

		return $methodExists;
	}

	/**
	 * Sets the view to be rendered when ready. Used to override the default route/method view.
	 *
	 * @param   $page   string   relative path of view to load from views folder
	 * @return  void
	 */
	public function setView($page) {
		$this->m_sView = $page;
	}

	/**
	 * Gets the currently set view path.
	 *
	 * @return  string
	 */
	public function getView() {
		return $this->m_sView;
	}

	/**
	 * Was the requested param sent and was it clean.
	 *
	 * @param   $key   string   parameter key
	 * @return  bool
	 */
	protected function isParam($key) {
		$result = false;
		if (array_key_exists($key, $this->m_Params)) {
			$result = true;
		}
		return $result;
	}

	/**
	 * Gets the requested sanitized parameter or false if not found.
	 *
	 * @param   $key   string   parameter key
	 * @return  mixed
	 */
	protected function getParam($key) {
		$result = '';

		if ($this->isParam($key)) {
			$result = $this->m_Params[$key];
		}

		return $result;
	}

	/**
	 * Adds an error or message type message to be displayed.
	 * Note: Messages do not bubble up to the application class.
	 *
	 * @param   string   $message   Message to be displayed to user
	 * @param   string   $class     Type of message to be added: error, message
	 */
	public function addMessage($message, $class = 'message') {
		$this->m_Messages[] = array($class, $message);
	}

	/**
	 * Gets all messages that need to be sent to the user.
	 *
	 * @return  array        Array of all array(type, message)
	 */
	public function getMessages() {
		return $this->m_Messages;
	}
}
?>
