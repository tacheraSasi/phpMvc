<?php 

namespace Controller;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * ProtectedApi class - Example of protected API endpoints
 */
class ProtectedApi
{
	use MainController;

	public function profile()
	{
		// This would typically have auth middleware applied
		\Core\Middleware\AuthMiddleware::handle();
		
		$user = $_REQUEST['authenticated_user'] ?? null;
		
		$this->sendSuccess($user, 'User profile retrieved');
	}

	public function dashboard()
	{
		// Apply auth middleware
		\Core\Middleware\AuthMiddleware::handle();
		
		$user = $_REQUEST['authenticated_user'] ?? null;
		
		$dashboardData = [
			'user' => $user,
			'stats' => [
				'total_users' => 1250,
				'active_sessions' => 45,
				'api_calls_today' => 2850
			],
			'recent_activity' => [
				'User logged in',
				'API call to /users',
				'Data updated'
			]
		];
		
		$this->sendSuccess($dashboardData, 'Dashboard data retrieved');
	}
}