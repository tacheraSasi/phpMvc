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
			'message' => 'Welcome to the Enhanced PHP MVC API',
			'version' => '1.0.0',
			'description' => 'A modern PHP MVC framework with NestJS-inspired features',
			'features' => [
				'RESTful routing with parameters',
				'JSON request/response handling',
				'CORS support',
				'JWT authentication',
				'Request validation',
				'Rate limiting',
				'Middleware system',
				'Dependency injection',
				'Global error handling'
			],
			'endpoints' => [
				'Authentication' => [
					'POST /api/auth/login' => 'User login',
					'POST /api/auth/register' => 'User registration',
					'GET /api/auth/me' => 'Get current user (requires auth)',
					'POST /api/auth/refresh' => 'Refresh token'
				],
				'Users' => [
					'GET /api/users' => 'Get all users',
					'GET /api/users/{id}' => 'Get user by ID',
					'POST /api/users' => 'Create new user',
					'PUT /api/users/{id}' => 'Update user',
					'DELETE /api/users/{id}' => 'Delete user'
				],
				'Protected' => [
					'GET /api/profile' => 'Get user profile (requires auth)',
					'GET /api/dashboard' => 'Get dashboard data (requires auth)'
				]
			],
			'examples' => [
				'login' => [
					'method' => 'POST',
					'url' => '/api/auth/login',
					'headers' => ['Content-Type: application/json'],
					'body' => [
						'email' => 'admin@example.com',
						'password' => 'password123'
					]
				],
				'create_user' => [
					'method' => 'POST',
					'url' => '/api/users',
					'headers' => ['Content-Type: application/json'],
					'body' => [
						'name' => 'John Doe',
						'email' => 'john@example.com'
					]
				],
				'protected_request' => [
					'method' => 'GET',
					'url' => '/api/profile',
					'headers' => ['Authorization: Bearer <token>']
				]
			],
			'demo_credentials' => [
				'email' => 'admin@example.com',
				'password' => 'password123'
			]
		]);
	}

	public function notFound()
	{
		$this->sendError('API endpoint not found', 404);
	}
}