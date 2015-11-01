<?php
class DefaultRouter extends Router
{
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->addData('world', 'hello world');
		$this->addData('myarr', array(1, 2, 3, 4, 5));

		$this->addData('links--id', '');
		$this->addData('links--link', '');
	}

	public function edit() {
		if ($this->inputs->exists('post', 'links--id')) {
			if (!$this->inputs->isClean('post', 'links--id')) {
				$this->addMessage($this->inputs->getError('post', 'links--id'), 'error');
			}
			else {
				$this->addMessage('Link ID set.');
				$this->addData('links--id', $this->inputs->post('links--id'));
			}
		}
		if ($this->inputs->exists('post', 'links--link')) {
			if (!$this->inputs->isClean('post', 'links--link')) {
				$this->addMessage($this->inputs->getError('post', 'links--link'), 'error');
			}
			else {
				$this->addMessage('Link URL set.');
				$this->addData('links--link', $this->inputs->post('links--link'));
			}
		}

		$this->setView('default/index');
		$this->index();
	}
}
?>
