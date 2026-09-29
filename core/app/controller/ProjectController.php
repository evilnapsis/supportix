<?php
namespace App\Controller;

use App\Service\ProjectService;
use ViewEngine;
use Req;

/**
 * Controlador para la gestión de proyectos en Supportix.
 */
class ProjectController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}

	public function index(): void {
		$projects = ProjectService::getAll();
		ViewEngine::render('projects/index.html.twig', [
			'projects' => $projects,
		]);
	}

	public function new(): void {
		ViewEngine::render('projects/new.html.twig');
	}

	public function create(): void {
		$res = ProjectService::create($_POST);
		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/project/new');
			exit;
		}

		$_SESSION['success'] = "¡Proyecto creado exitosamente!";
		header('Location: ' . $this->baseFolder . '/projects');
		exit;
	}

	public function tickets(array $params): void {
		$id = (int)($params['id'] ?? 0);
		$project = ProjectService::getById($id);

		if (!$project) {
			$_SESSION['error'] = "El proyecto solicitado no existe.";
			header('Location: ' . $this->baseFolder . '/projects');
			exit;
		}

		$filters = [
			'project_id'  => $id,
			'status_id'   => Req::get('status_id', ''),
			'category_id' => Req::get('category_id', ''),
			'q'           => Req::get('q', ''),
		];

		$tickets = \TicketData::getByFilter($filters);

		// Conteo de tickets por status específico para este proyecto
		$db = \Database::getPdo();
		$sqlStatus = "SELECT s.id, s.name, COUNT(t.id) AS total
					  FROM status s
					  LEFT JOIN ticket t ON t.status_id = s.id AND t.project_id = :pid
					  GROUP BY s.id, s.name
					  ORDER BY s.id ASC";
		$stmtStatus = $db->prepare($sqlStatus);
		$stmtStatus->execute(['pid' => $id]);
		$statusCounts = $stmtStatus->fetchAll(\PDO::FETCH_ASSOC);

		$categories = \CategoryData::getAll();
		$statuses = \StatusData::getAll();

		ViewEngine::render('projects/tickets.html.twig', [
			'project'       => $project,
			'tickets'       => $tickets,
			'status_counts' => $statusCounts,
			'categories'    => $categories,
			'statuses'      => $statuses,
			'filters'       => $filters,
		]);
	}

	public function edit(array $params): void {
		$id = (int)($params['id'] ?? 0);
		$project = ProjectService::getById($id);

		if (!$project) {
			$_SESSION['error'] = "El proyecto no existe.";
			header('Location: ' . $this->baseFolder . '/projects');
			exit;
		}

		ViewEngine::render('projects/edit.html.twig', [
			'project' => $project,
		]);
	}

	public function update(array $params): void {
		$id = (int)($params['id'] ?? 0);
		$res = ProjectService::update($id, $_POST);

		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/project/' . $id . '/edit');
			exit;
		}

		$_SESSION['updated'] = "¡Proyecto actualizado exitosamente!";
		header('Location: ' . $this->baseFolder . '/projects');
		exit;
	}

	public function delete(array $params): void {
		$id = (int)($params['id'] ?? 0);
		ProjectService::delete($id);

		$_SESSION['deleted'] = "¡Proyecto eliminado exitosamente!";
		header('Location: ' . $this->baseFolder . '/projects');
		exit;
	}
}
