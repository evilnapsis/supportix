<?php
namespace App\Controller;

use App\Service\UserService;
use App\Service\AuthService;
use ViewEngine;
use Req;

/**
 * Controla la vista de Mi Perfil y el cambio de contraseña en Supportix.
 */
class ProfileController {
	private UserService $userService;
	private string $baseFolder;

	public function __construct() {
		$this->userService = new UserService();
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}

	public function index(): void {
		$user = AuthService::user();
		ViewEngine::render('profile/index.html.twig', [
			'user' => $user,
		]);
	}

	public function changePassword(): void {
		$userId = AuthService::id();
		if (!$userId) {
			header('Location: ' . $this->baseFolder . '/login');
			exit;
		}

		$currentPassword = Req::post('password', '');
		$newPassword = Req::post('newpassword', '');
		$confirmPassword = Req::post('confirmnewpassword', '');

		if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
			$_SESSION['error'] = 'Debes llenar todos los campos de contraseña.';
			header('Location: ' . $this->baseFolder . '/profile');
			exit;
		}

		if ($newPassword !== $confirmPassword) {
			$_SESSION['error'] = 'La nueva contraseña y su confirmación no coinciden.';
			header('Location: ' . $this->baseFolder . '/profile');
			exit;
		}

		$res = $this->userService->changePassword($userId, $currentPassword, $newPassword);
		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/profile');
			exit;
		}

		$_SESSION['success'] = '¡Contraseña actualizada correctamente!';
		header('Location: ' . $this->baseFolder . '/profile');
		exit;
	}
}
