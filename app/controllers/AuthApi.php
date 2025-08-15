<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * AuthApi class - Authentication endpoints
 */
class AuthApi
{
	use MainController;

	public function login()
	{
		// POST /api/auth/login
		$req = new \Luna\Request;
		$data = $req->allInput();
		
		// Validate request data
		$validator = \Validator::make($data, [
			'email' => 'required|email',
			'password' => 'required|min:6'
		]);
		
		if ($validator->fails()) {
			$this->sendError('Validation failed', 422, $validator->getErrors());
		}
		
		// Simulate user authentication (replace with actual database lookup)
		$users = [
			'admin@example.com' => [
				'id' => 1,
				'name' => 'Admin User',
				'email' => 'admin@example.com',
				'password' => password_hash('password123', PASSWORD_DEFAULT)
			],
			'user@example.com' => [
				'id' => 2,
				'name' => 'Regular User',
				'email' => 'user@example.com',
				'password' => password_hash('userpass', PASSWORD_DEFAULT)
			]
		];
		
		$user = $users[$data['email']] ?? null;
		
		if (!$user || !password_verify($data['password'], $user['password'])) {
			$this->sendError('Invalid credentials', 401);
		}
		
		// Generate JWT token
		require_once "../app/core/middleware/AuthMiddleware.php";
		$token = \Core\Middleware\AuthMiddleware::generateToken($user);
		
		$this->sendSuccess([
			'token' => $token,
			'token_type' => 'Bearer',
			'expires_in' => \Config::get('security.jwt.expiry', 3600),
			'user' => [
				'id' => $user['id'],
				'name' => $user['name'],
				'email' => $user['email']
			]
		], 'Login successful');
	}
	
	public function register()
	{
		// POST /api/auth/register
		$req = new \Luna\Request;
		$data = $req->allInput();
		
		// Validate request data
		$validator = \Validator::make($data, [
			'name' => 'required|min:2|max:50',
			'email' => 'required|email|max:100',
			'password' => 'required|min:6|max:50'
		]);
		
		if ($validator->fails()) {
			$this->sendError('Validation failed', 422, $validator->getErrors());
		}
		
		// Simulate user creation (replace with actual database insert)
		$newUser = [
			'id' => rand(100, 999),
			'name' => $data['name'],
			'email' => $data['email'],
			'created_at' => date('Y-m-d H:i:s')
		];
		
		// Generate JWT token
		require_once "../app/core/middleware/AuthMiddleware.php";
		$token = \Core\Middleware\AuthMiddleware::generateToken($newUser);
		
		$this->sendSuccess([
			'token' => $token,
			'token_type' => 'Bearer',
			'expires_in' => \Config::get('security.jwt.expiry', 3600),
			'user' => $newUser
		], 'Registration successful', 201);
	}
	
	public function me()
	{
		// GET /api/auth/me - Get current user (requires authentication)
		
		// This endpoint would typically have auth middleware applied
		$user = $_REQUEST['authenticated_user'] ?? null;
		
		if (!$user) {
			$this->sendError('Authentication required', 401);
		}
		
		$this->sendSuccess($user, 'User profile retrieved');
	}
	
	public function refresh()
	{
		// POST /api/auth/refresh - Refresh token
		$user = $_REQUEST['authenticated_user'] ?? null;
		
		if (!$user) {
			$this->sendError('Authentication required', 401);
		}
		
		// Generate new token
		require_once "../app/core/middleware/AuthMiddleware.php";
		$token = \Core\Middleware\AuthMiddleware::generateToken($user);
		
		$this->sendSuccess([
			'token' => $token,
			'token_type' => 'Bearer',
			'expires_in' => \Config::get('security.jwt.expiry', 3600)
		], 'Token refreshed');
	}
}