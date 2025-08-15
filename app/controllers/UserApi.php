<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * UserApi class - RESTful API for user management
 */
class UserApi
{
	use MainController;

	public function index()
	{
		// GET /api/users - Get all users
		$users = [
			['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
			['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
			['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com']
		];
		
		$this->sendSuccess($users, 'Users retrieved successfully');
	}

	public function show($id)
	{
		// GET /api/users/{id} - Get user by ID
		$users = [
			1 => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
			2 => ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
			3 => ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com']
		];
		
		if (!isset($users[$id])) {
			$this->sendError('User not found', 404);
		}
		
		$this->sendSuccess($users[$id], 'User retrieved successfully');
	}

	public function store()
	{
		// POST /api/users - Create new user
		$req = new \Luna\Request;
		$data = $req->allInput();
		
		// Validate request data
		$validator = \Validator::make($data, [
			'name' => 'required|min:2|max:50',
			'email' => 'required|email|max:100'
		]);
		
		if ($validator->fails()) {
			$this->sendError('Validation failed', 422, $validator->getErrors());
		}
		
		// Simulate creating user
		$newUser = [
			'id' => rand(100, 999),
			'name' => $data['name'],
			'email' => $data['email'],
			'created_at' => date('Y-m-d H:i:s')
		];
		
		$this->sendSuccess($newUser, 'User created successfully', 201);
	}

	public function update($id)
	{
		// PUT /api/users/{id} - Update user
		$req = new \Luna\Request;
		$data = $req->allInput();
		
		// Validate request data
		$validator = \Validator::make($data, [
			'name' => 'min:2|max:50',
			'email' => 'email|max:100'
		]);
		
		if ($validator->fails()) {
			$this->sendError('Validation failed', 422, $validator->getErrors());
		}
		
		// Simulate updating user
		$updatedUser = [
			'id' => (int)$id,
			'name' => $data['name'] ?? 'Updated Name',
			'email' => $data['email'] ?? 'updated@example.com',
			'updated_at' => date('Y-m-d H:i:s')
		];
		
		$this->sendSuccess($updatedUser, 'User updated successfully');
	}

	public function destroy($id)
	{
		// DELETE /api/users/{id} - Delete user
		$this->sendSuccess([], 'User deleted successfully');
	}
}