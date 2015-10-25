<?php
class DefaultRouter extends Router
{
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->addData('world', 'hello world');
		$this->addData('myarr', array(1, 2, 3, 4, 5));
	}
}
?>
