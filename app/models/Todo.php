<?php

namespace Model;

defined('ROOTPATH') OR exit('Access Denied!');

#Todo class
class Todo{
	
	use Model;

	protected $table = 'todos';
	protected $primaryKey = 'id';
	protected $loginUniqueColumn = 'email';

	protected $allowedColumns = [
		
	];

	/*****************************
	 * 	rules include:
		required
		alpha
		email
		numeric
		unique
		symbol
		longer_than_8_chars
		alpha_numeric_symbol
		alpha_numeric
		alpha_symbol
	 * 
	 ****************************/
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

}