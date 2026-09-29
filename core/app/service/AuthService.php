<?php
namespace App\Service;

use UserData;

/**
 * Servicio de autenticación y control de acceso en Supportix.
 */
class AuthService {

	public static function check(): bool {
		return !empty($_SESSION['user_id']);
	}

	public static function id(): ?int {
		return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
	}

	public static function user(): ?UserData {
		if (!self::check()) {
			return null;
		}
		return UserData::getById((int)$_SESSION['user_id']);
	}

	public static function isAdmin(): bool {
		$user = self::user();
		return $user !== null && (int)$user->kind === 1;
	}

	public static function attempt(string $login, string $password): array {
		if (empty($login) || empty($password)) {
			return ['success' => false, 'error' => 'Por favor, ingresa tu usuario y contraseña.'];
		}

		$user = UserData::getByUsernameOrEmail($login);
		if (!$user) {
			return ['success' => false, 'error' => 'Usuario o contraseña incorrectos.'];
		}

		$hashed = sha1(md5($password));
		if ($user->password !== $hashed) {
			return ['success' => false, 'error' => 'Usuario o contraseña incorrectos.'];
		}

		if (isset($user->is_active) && (int)$user->is_active === 0) {
			return ['success' => false, 'error' => 'Tu cuenta se encuentra desactivada. Contacta al administrador.'];
		}

		$_SESSION['user_id'] = (int)$user->id;
		return ['success' => true, 'user' => $user];
	}

	public static function logout(): void {
		if (isset($_SESSION['user_id'])) {
			unset($_SESSION['user_id']);
		}
		session_destroy();
	}
}
