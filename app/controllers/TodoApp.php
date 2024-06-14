<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * TodoApp class
 */
class TodoApp
{
	use MainController;

	public function index(){
		$todos = [
			'Build ekiliSense',
			'Test mvc framework',
			'Learn Django',
			'mernStack'
		];
		$context = ["todos"=>$todos];
		$this->view('todoapp',$context);

		return $this->todos;
	}
	public function new(){
		$this->view('new-todo');
	}
	public function add(){
		redirect_to('../todo');
	}

}
