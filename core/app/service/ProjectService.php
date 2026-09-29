<?php
namespace App\Service;

use ProjectData;

/**
 * Servicio de negocio para la administración de proyectos en Supportix.
 */
class ProjectService {

	public static function getAll(): array {
		return ProjectData::getAll();
	}

	public static function getById(int $id): ?ProjectData {
		return ProjectData::getById($id);
	}

	public static function create(array $data): array {
		$name = trim($data['name'] ?? '');
		if (empty($name)) {
			return ['success' => false, 'error' => 'El nombre del proyecto es obligatorio.'];
		}

		$project = new ProjectData();
		$project->name = $name;
		$project->description = trim($data['description'] ?? '');
		$project->add();

		return ['success' => true, 'project' => $project];
	}

	public static function update(int $id, array $data): array {
		$project = ProjectData::getById($id);
		if (!$project) {
			return ['success' => false, 'error' => 'Proyecto no encontrado.'];
		}

		$name = trim($data['name'] ?? '');
		if (empty($name)) {
			return ['success' => false, 'error' => 'El nombre del proyecto es obligatorio.'];
		}

		$project->name = $name;
		$project->description = trim($data['description'] ?? '');
		$project->update();

		return ['success' => true, 'project' => $project];
	}

	public static function delete(int $id): bool {
		$project = ProjectData::getById($id);
		if ($project) {
			$project->del();
			return true;
		}
		return false;
	}
}
