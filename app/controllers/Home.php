<?php 
namespace Controller;
defined('ROOTPATH') OR exit('Access Denied!');

/**
 * home class
 */
class Home
{
	use MainController;

	public function index()
	{
		$context = [
			"users"=>[
				"Tach","Sasi"
			],
			"value"=>2000
		];
		$this->view('home',$context);
	}
	public function new(){
		$data = [
			"first"=>"Tachera W",
			"second"=>"Mwera W",
			"third"=>"Sasi W",
			"last"=>"Sky W"
		];
		$this->sendSuccess($data, 'Data retrieved successfully');
	}

}
