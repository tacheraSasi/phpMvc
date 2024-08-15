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

	}
	public function new(){
		$this->view('new-todo');
	}
	public function add(){
		$db = new \Model\Database;
		$conn = $db->connect;
		$req = new \Luna\Request;
		$todo = $req->post('todo');
		$query = "insert into todos (todo)values($todo)";
		$add = mysqli_query($conn,$query);
		if($add->add()){
			redirect_to('../todo');
		}else{
			echo "something went wrong";
			
		}
	}

}
