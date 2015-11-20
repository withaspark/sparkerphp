<?php if (!defined('INCLUDES_OK')) die ('Invalid SparkerPHP configuration.');
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
				$this->addMessage('Link ID set.', 'confirm');
				$this->addData('links--id', $this->inputs->post('links--id'));
			}
		}
		if ($this->inputs->exists('post', 'links--link')) {
			if (!$this->inputs->isClean('post', 'links--link')) {
				$this->addMessage($this->inputs->getError('post', 'links--link'), 'error');
			}
			else {
				$this->addMessage('Link URL set.', 'confirm');
				$this->addData('links--link', $this->inputs->post('links--link'));
			}
		}
		if ($this->inputs->exists('file', 'links--file-0')) {
			if (!$this->inputs->isClean('file', 'links--file-0')) {
				$this->addMessage($this->inputs->getError('file', 'links--file-0'), 'error');
			}
			else {
				if ($this->inputs->file('links--file-0')->save())
					$this->addMessage('File '.$this->inputs->file('links--file-0')->getFilename().' received.', 'confirm');
				else
					$this->addMessage('Couldn\'t save '.$this->inputs->file('links--file-0')->getFilename().'.', 'error');
			}
		}
		if ($this->inputs->exists('file', 'links--file-1')) {
			if (!$this->inputs->isClean('file', 'links--file-1')) {
				$this->addMessage($this->inputs->getError('file', 'links--file-1'), 'error');
			}
			else {
				if ($this->inputs->file('links--file-1')->save())
					$this->addMessage('File '.$this->inputs->file('links--file-1')->getFilename().' received.', 'confirm');
				else
					$this->addMessage('Couldn\'t save '.$this->inputs->file('links--file-1')->getFilename().'.', 'error');
			}
		}

		$this->setView('default/index');
		$this->index();
	}
}
?>
