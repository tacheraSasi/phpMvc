<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Luna class
 */
class Luna{
	use MainController;

	public function index(){
		$name = 'lunaPHP';
		$context = [$name];

		$this->view('luna',$context);
	}
	public function render(){
		$data = [
			'lunaPHP',
			'Laravel',
			'cakePHP',
		];
		$this->renderJSON($data);
	}

}
