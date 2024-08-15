<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

#Authenticator class
 
class Authenticator{
	use MainController;

	public function index(){
		$this->view('authenticator');
	}

}
