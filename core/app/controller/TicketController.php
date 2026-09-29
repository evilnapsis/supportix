<?php
namespace App\Controller;

use App\Service\TicketService;
use App\Service\AuthService;
use ViewEngine;
use Req;

/**
 * Controlador para la gestión de tickets en Supportix.
 */
class TicketController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}

	public function index(): void {
		$filters = [
			'q'           => Req::get('q', ''),
			'project_id'  => Req::get('project_id', ''),
			'category_id' => Req::get('category_id', ''),
			'status_id'   => Req::get('status_id', ''),
			'date_at'     => Req::get('date_at', ''),
		];

		$tickets = TicketService::getFiltered($filters);
		$catalogs = TicketService::getCatalogs();
		$statusCounts = \TicketData::getCountsByStatus();

		ViewEngine::render('tickets/index.html.twig', [
			'tickets'       => $tickets,
			'projects'      => $catalogs['projects'],
			'categories'    => $catalogs['categories'],
			'priorities'    => $catalogs['priorities'],
			'statuses'      => $catalogs['statuses'],
			'kinds'         => $catalogs['kinds'],
			'filters'       => $filters,
			'status_counts' => $statusCounts,
		]);
	}

	public function new(): void {
		$catalogs = TicketService::getCatalogs();
		ViewEngine::render('tickets/new.html.twig', [
			'projects'            => $catalogs['projects'],
			'categories'          => $catalogs['categories'],
			'priorities'          => $catalogs['priorities'],
			'statuses'            => $catalogs['statuses'],
			'kinds'               => $catalogs['kinds'],
			'selected_project_id' => Req::get('project_id', ''),
		]);
	}

	public function create(): void {
		$userId = AuthService::id() ?: 1;
		$res = TicketService::create($_POST, $userId);

		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/ticket/new');
			exit;
		}

		$_SESSION['success'] = "¡Ticket creado exitosamente!";
		header('Location: ' . $this->baseFolder . '/tickets');
		exit;
	}

	public function edit(array $params): void {
		$id = (int)($params['id'] ?? 0);
		$ticket = TicketService::getById($id);

		if (!$ticket) {
			$_SESSION['error'] = "El ticket solicitado no existe.";
			header('Location: ' . $this->baseFolder . '/tickets');
			exit;
		}

		$catalogs = TicketService::getCatalogs();
		ViewEngine::render('tickets/edit.html.twig', [
			'ticket'     => $ticket,
			'projects'   => $catalogs['projects'],
			'categories' => $catalogs['categories'],
			'priorities' => $catalogs['priorities'],
			'statuses'   => $catalogs['statuses'],
			'kinds'      => $catalogs['kinds'],
		]);
	}

	public function update(array $params): void {
		$id = (int)($params['id'] ?? 0);
		$res = TicketService::update($id, $_POST);

		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/ticket/' . $id . '/edit');
			exit;
		}

		$_SESSION['updated'] = "¡Ticket actualizado exitosamente!";
		header('Location: ' . $this->baseFolder . '/tickets');
		exit;
	}

	public function delete(array $params): void {
		$id = (int)($params['id'] ?? 0);
		TicketService::delete($id);

		$_SESSION['deleted'] = "¡Ticket eliminado exitosamente!";
		header('Location: ' . $this->baseFolder . '/tickets');
		exit;
	}
}
