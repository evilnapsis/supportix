<?php
namespace App\Service;

use CategoryData;

/**
 * Servicio de negocio para la gestión de categorías en Supportix.
 */
class CategoryService {

	public static function getAll(): array {
		return CategoryData::getAll();
	}

	public static function getById(int $id): ?CategoryData {
		return CategoryData::getById($id);
	}

	public static function create(array $data): array {
		$name = trim($data['name'] ?? '');
		if (empty($name)) {
			return ['success' => false, 'error' => 'El nombre de la categoría es obligatorio.'];
		}

		$cat = new CategoryData();
		$cat->name = $name;
		$cat->add();

		return ['success' => true, 'category' => $cat];
	}

	public static function update(int $id, array $data): array {
		$cat = CategoryData::getById($id);
		if (!$cat) {
			return ['success' => false, 'error' => 'Categoría no encontrada.'];
		}

		$name = trim($data['name'] ?? '');
		if (empty($name)) {
			return ['success' => false, 'error' => 'El nombre de la categoría es obligatorio.'];
		}

		$cat->name = $name;
		$cat->update();

		return ['success' => true, 'category' => $cat];
	}

	public static function delete(int $id): bool {
		$cat = CategoryData::getById($id);
		if ($cat) {
			$cat->del();
			return true;
		}
		return false;
	}
}
