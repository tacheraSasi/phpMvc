<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * TodoApp class
 */
class TodoApp
{
	use MainController;

	public function index()
	{

		$this->view('todoapp');
	}

}
