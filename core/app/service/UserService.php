<?php
namespace App\Service;

use UserData;

/**
 * Servicio de negocio para la administración de usuarios y contraseñas en Supportix.
 */
class UserService {

	public function getAllUsers(): array {
		return UserData::getAll();
	}

	public function getUserById($id): ?UserData {
		return UserData::getById((int)$id);
	}

	public function createUser(array $data): array {
		$username = trim($data['username'] ?? '');
		$password = $data['password'] ?? '';
		$kind = !empty($data['kind']) ? (int)$data['kind'] : (!empty($data['is_admin']) ? 1 : 2);

		if (empty($username)) {
			return ['success' => false, 'error' => 'El nombre de usuario es obligatorio.'];
		}
		if (empty($password)) {
			return ['success' => false, 'error' => 'La contraseña es obligatoria.'];
		}

		$existing = UserData::getByUsernameOrEmail($username);
		if ($existing) {
			return ['success' => false, 'error' => 'El nombre de usuario ya está registrado.'];
		}

		$user = new UserData();
		$user->name = trim($data['name'] ?? '');
		$user->lastname = trim($data['lastname'] ?? '');
		$user->username = $username;
		$user->email = trim($data['email'] ?? '');
		$user->kind = $kind;
		$user->is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
		$user->password = sha1(md5($password));
		$user->add();

		return ['success' => true, 'user' => $user];
	}

	public function updateUser($id, array $data): array {
		$user = UserData::getById((int)$id);
		if (!$user) {
			return ['success' => false, 'error' => 'Usuario no encontrado.'];
		}

		$username = trim($data['username'] ?? '');
		if (empty($username)) {
			return ['success' => false, 'error' => 'El nombre de usuario es obligatorio.'];
		}

		$user->name = trim($data['name'] ?? $user->name);
		$user->lastname = trim($data['lastname'] ?? $user->lastname);
		$user->username = $username;
		$user->email = trim($data['email'] ?? $user->email);
		if (isset($data['kind'])) {
			$user->kind = (int)$data['kind'];
		} elseif (isset($data['is_admin'])) {
			$user->kind = !empty($data['is_admin']) ? 1 : 2;
		}
		if (isset($data['is_active'])) {
			$user->is_active = (int)$data['is_active'];
		}
		$user->update();

		if (!empty($data['password'])) {
			$user->password = sha1(md5($data['password']));
			$user->update_passwd();
		}

		return ['success' => true, 'user' => $user];
	}

	public function deleteUser($id, $currentUserId): bool {
		if ((int)$id === (int)$currentUserId) {
			return false;
		}
		UserData::delById((int)$id);
		return true;
	}

	public function changePassword(int $userId, string $currentPassword, string $newPassword): array {
		$user = UserData::getById($userId);
		if (!$user) {
			return ['success' => false, 'error' => 'Usuario no encontrado.'];
		}

		if (sha1(md5($currentPassword)) !== $user->password) {
			return ['success' => false, 'error' => 'La contraseña actual no es correcta.'];
		}

		$user->password = sha1(md5($newPassword));
		$user->update_passwd();

		return ['success' => true];
	}
}
