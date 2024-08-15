<?php

namespace Model;

defined('ROOTPATH') OR exit('Access Denied!');

#Todo class
class Todo{
	
	use Model;

	protected $table = 'todos';
	protected $primaryKey = 'id';

	protected $allowedColumns = [
		
	];

	protected $validationRules = [

		'email' => [
			'email',
			'unique',
			'required',
		],
		'username' => [
			'alpha',
			'required',
		],
		'password' => [
			'not_less_than_8_chars',
			'required',
		],
	];

	public function add(){
		$req = new \Core\Request;
		$todo = $req->post('todo');
		if($this->insert(['todo'=>$todo])){
			return True;
		}else{
			return False;
		}
	}
	
	public function getAllTodos(){
		return $this->getAll();
	}

}