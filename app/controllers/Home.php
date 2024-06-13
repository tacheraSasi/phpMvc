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

		$this->view('home');
	}
	public function new(){
		$data = [
			"first"=>"Tachera W",
			"second"=>"Mwera W",
			"third"=>"Sasi W",
			"last"=>"Sky W"
		];
		$this->renderJSON($data);
	}

}
