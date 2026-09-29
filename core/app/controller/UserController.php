<?php
namespace App\Controller;

use App\Service\UserService;
use App\Service\AuthService;
use ViewEngine;
use Req;

/**
 * Controla la administración de usuarios del sistema en Supportix.
 */
class UserController {
	private UserService $userService;
	private string $baseFolder;

	public function __construct() {
		$this->userService = new UserService();
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}

	private function checkAdmin(): void {
		if (!AuthService::isAdmin()) {
			$_SESSION['error'] = 'Acceso denegado. Se requieren permisos de administrador.';
			header('Location: ' . $this->baseFolder . '/home');
			exit;
		}
	}

	public function index(): void {
		$this->checkAdmin();
		ViewEngine::render('users/index.html.twig', [
			'users' => $this->userService->getAllUsers(),
		]);
	}

	public function new(): void {
		$this->checkAdmin();
		ViewEngine::render('users/new.html.twig');
	}

	public function create(): void {
		$this->checkAdmin();
		$res = $this->userService->createUser($_POST);
		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/user/new');
			exit;
		}

		$_SESSION['success'] = '¡Usuario agregado correctamente!';
		header('Location: ' . $this->baseFolder . '/users');
		exit;
	}

	public function edit(array $params): void {
		$this->checkAdmin();
		$id = (int)($params['id'] ?? 0);
		$user = $this->userService->getUserById($id);
		if (!$user) {
			$_SESSION['error'] = 'El usuario no existe.';
			header('Location: ' . $this->baseFolder . '/users');
			exit;
		}

		ViewEngine::render('users/edit.html.twig', [
			'user' => $user,
		]);
	}

	public function update(array $params): void {
		$this->checkAdmin();
		$id = (int)($params['id'] ?? 0);
		$res = $this->userService->updateUser($id, $_POST);
		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/user/' . $id . '/edit');
			exit;
		}

		$_SESSION['updated'] = '¡Usuario actualizado correctamente!';
		header('Location: ' . $this->baseFolder . '/users');
		exit;
	}

	public function delete(array $params): void {
		$this->checkAdmin();
		$id = (int)($params['id'] ?? 0);
		$currentUserId = AuthService::id();

		if ($id === $currentUserId) {
			$_SESSION['error'] = 'No puedes eliminar tu propia cuenta de usuario.';
			header('Location: ' . $this->baseFolder . '/users');
			exit;
		}

		$this->userService->deleteUser($id, $currentUserId);
		$_SESSION['deleted'] = '¡Usuario eliminado correctamente!';
		header('Location: ' . $this->baseFolder . '/users');
		exit;
	}
}
