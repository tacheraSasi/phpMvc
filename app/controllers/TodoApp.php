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
		
		// Check if this is an API request
		$req = new \Luna\Request;
		if ($req->method() === 'GET' && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
			$this->sendSuccess($todos, 'Todos retrieved successfully');
		} else {
			$this->view('todoapp',$context);
		}
	}
	public function new(){
		$this->view('new-todo');
	}
	public function add(){
		$req = new \Luna\Request;
		$todo = $req->input('todo');
		
		if(empty($todo)) {
			$this->sendError('Todo is required', 400);
		}
		
		// Simulate database operation (replace with actual DB logic)
		$result = true; // $db->insert(['todo' => $todo]);
		
		if($result){
			$this->sendSuccess(['todo' => $todo], 'Todo added successfully', 201);
		}else{
			$this->sendError('Failed to add todo', 500);
		}
	}

}
