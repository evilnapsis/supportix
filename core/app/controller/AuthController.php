<?php
namespace App\Controller;

use App\Service\AuthService;
use ViewEngine;
use Req;

/**
 * Controla el inicio y cierre de sesión de usuarios en Supportix.
 */
class AuthController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}

	public function showLogin(): void {
		if (AuthService::check()) {
			header('Location: ' . $this->baseFolder . '/home');
			exit;
		}
		ViewEngine::render('auth/login.html.twig');
	}

	public function processLogin(): void {
		$username = trim(Req::post('username', ''));
		$password = Req::post('password', '');

		$res = AuthService::attempt($username, $password);
		if ($res['success']) {
			header('Location: ' . $this->baseFolder . '/home');
			exit;
		}

		ViewEngine::render('auth/login.html.twig', [
			'error'    => $res['error'],
			'username' => $username,
		]);
	}

	public function logout(): void {
		AuthService::logout();
		header('Location: ' . $this->baseFolder . '/login');
		exit;
	}
}
