<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * Api class - Example RESTful API controller
 */
class Api
{
	use MainController;

	public function index()
	{
		$this->sendSuccess([
			'message' => 'Welcome to the API',
			'version' => '1.0.0',
			'endpoints' => [
				'GET /api/users' => 'Get all users',
				'GET /api/users/{id}' => 'Get user by ID',
				'POST /api/users' => 'Create new user',
				'PUT /api/users/{id}' => 'Update user',
				'DELETE /api/users/{id}' => 'Delete user'
			]
		]);
	}

	public function notFound()
	{
		$this->sendError('API endpoint not found', 404);
	}
}