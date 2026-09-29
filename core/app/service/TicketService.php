<?php
namespace App\Service;

use TicketData;
use ProjectData;
use CategoryData;
use PriorityData;
use StatusData;
use KindData;

/**
 * Servicio de negocio para la gestión integral de tickets en Supportix.
 */
class TicketService {

	public static function getFiltered(array $filters = []): array {
		$cleanFilters = [];
		if (!empty($filters['q'])) {
			$cleanFilters['q'] = trim($filters['q']);
		}
		if (!empty($filters['project_id'])) {
			$cleanFilters['project_id'] = (int)$filters['project_id'];
		}
		if (!empty($filters['category_id'])) {
			$cleanFilters['category_id'] = (int)$filters['category_id'];
		}
		if (!empty($filters['priority_id'])) {
			$cleanFilters['priority_id'] = (int)$filters['priority_id'];
		}
		if (!empty($filters['status_id'])) {
			$cleanFilters['status_id'] = (int)$filters['status_id'];
		}
		if (!empty($filters['kind_id'])) {
			$cleanFilters['kind_id'] = (int)$filters['kind_id'];
		}
		if (!empty($filters['date_at'])) {
			$cleanFilters['date_at'] = trim($filters['date_at']);
		}

		return TicketData::getByFilter($cleanFilters);
	}

	public static function getById(int $id): ?TicketData {
		return TicketData::getById($id);
	}

	public static function getCatalogs(): array {
		return [
			'projects'   => ProjectData::getAll(),
			'categories' => CategoryData::getAll(),
			'priorities' => PriorityData::getAll(),
			'statuses'   => StatusData::getAll(),
			'kinds'      => KindData::getAll(),
		];
	}

	public static function create(array $data, int $userId): array {
		$title = trim($data['title'] ?? '');
		$description = trim($data['description'] ?? '');

		if (empty($title)) {
			return ['success' => false, 'error' => 'El título del ticket es obligatorio.'];
		}

		$ticket = new TicketData();
		$ticket->title = $title;
		$ticket->description = $description;
		$ticket->kind_id = !empty($data['kind_id']) ? (int)$data['kind_id'] : 1;
		$ticket->project_id = !empty($data['project_id']) ? (int)$data['project_id'] : null;
		$ticket->category_id = !empty($data['category_id']) ? (int)$data['category_id'] : null;
		$ticket->priority_id = !empty($data['priority_id']) ? (int)$data['priority_id'] : 1;
		$ticket->status_id = !empty($data['status_id']) ? (int)$data['status_id'] : 1;
		$ticket->user_id = $userId;
		$ticket->add();

		return ['success' => true, 'ticket' => $ticket];
	}

	public static function update(int $id, array $data): array {
		$ticket = TicketData::getById($id);
		if (!$ticket) {
			return ['success' => false, 'error' => 'Ticket no encontrado.'];
		}

		$title = trim($data['title'] ?? '');
		if (empty($title)) {
			return ['success' => false, 'error' => 'El título del ticket es obligatorio.'];
		}

		$ticket->title = $title;
		$ticket->description = trim($data['description'] ?? '');
		$ticket->kind_id = !empty($data['kind_id']) ? (int)$data['kind_id'] : 1;
		$ticket->project_id = !empty($data['project_id']) ? (int)$data['project_id'] : null;
		$ticket->category_id = !empty($data['category_id']) ? (int)$data['category_id'] : null;
		$ticket->priority_id = !empty($data['priority_id']) ? (int)$data['priority_id'] : 1;
		$ticket->status_id = !empty($data['status_id']) ? (int)$data['status_id'] : 1;
		$ticket->update();

		return ['success' => true, 'ticket' => $ticket];
	}

	public static function delete(int $id): bool {
		$ticket = TicketData::getById($id);
		if ($ticket) {
			$ticket->del();
			return true;
		}
		return false;
	}
}
