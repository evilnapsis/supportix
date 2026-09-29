<?php
namespace App\Service;

use TicketData;
use ProjectData;
use PriorityData;
use StatusData;
use KindData;
use CategoryData;

/**
 * Servicio de reportes de tickets para Supportix.
 */
class ReportService {

	public static function getCatalogs(): array {
		return [
			'projects'   => ProjectData::getAll(),
			'categories' => CategoryData::getAll(),
			'priorities' => PriorityData::getAll(),
			'statuses'   => StatusData::getAll(),
			'kinds'      => KindData::getAll(),
		];
	}

	public static function generateReport(array $filters = []): array {
		$cleanFilters = [];

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
		if (!empty($filters['start_at'])) {
			$cleanFilters['start_at'] = trim($filters['start_at']);
		}
		if (!empty($filters['finish_at'])) {
			$cleanFilters['finish_at'] = trim($filters['finish_at']);
		}

		return TicketData::getByFilter($cleanFilters);
	}

	public static function getReportData(array $filters = []): array {
		$tickets = self::generateReport($filters);
		$catalogs = self::getCatalogs();

		$defaultStatusColors = [
			1 => '#f9b115', // Pendiente (Warning)
			2 => '#321fdb', // En Desarrollo (Primary)
			3 => '#2eb85c', // Terminado (Success)
			4 => '#e55353', // Cancelado (Danger)
		];

		// 1. Contadores y distribución por Estado
		$statusCounts = [];
		foreach ($catalogs['statuses'] as $s) {
			$statusCounts[$s->id] = [
				'id'    => $s->id,
				'name'  => $s->name,
				'total' => 0,
				'color' => $defaultStatusColors[$s->id] ?? '#6c757d',
			];
		}

		// 2. Mapeos para Proyecto y Categoría
		$projectMap = [];
		foreach ($catalogs['projects'] as $p) {
			$projectMap[$p->id] = $p->name;
		}

		$categoryMap = [];
		foreach ($catalogs['categories'] as $c) {
			$categoryMap[$c->id] = $c->name;
		}

		$projectCounts = [];
		$categoryCounts = [];

		foreach ($tickets as $t) {
			// Conteo por estado
			$sid = (int)$t->status_id;
			if (isset($statusCounts[$sid])) {
				$statusCounts[$sid]['total']++;
			} else {
				$statusCounts[$sid] = [
					'id'    => $sid,
					'name'  => $t->status ? $t->status->name : 'Estado ' . $sid,
					'total' => 1,
					'color' => '#6c757d',
				];
			}

			// Conteo por proyecto
			$pid = (int)$t->project_id;
			$pname = $pid && isset($projectMap[$pid]) ? $projectMap[$pid] : ($t->project ? $t->project->name : 'Sin Proyecto');
			if (!isset($projectCounts[$pname])) {
				$projectCounts[$pname] = 0;
			}
			$projectCounts[$pname]++;

			// Conteo por categoría
			$cid = (int)$t->category_id;
			$cname = $cid && isset($categoryMap[$cid]) ? $categoryMap[$cid] : ($t->category ? $t->category->name : 'Sin Categoría');
			if (!isset($categoryCounts[$cname])) {
				$categoryCounts[$cname] = 0;
			}
			$categoryCounts[$cname]++;
		}

		// Preparar Gráfica Estado
		$statusLabels = [];
		$statusData = [];
		$statusColors = [];
		foreach ($statusCounts as $sc) {
			$statusLabels[] = $sc['name'];
			$statusData[] = $sc['total'];
			$statusColors[] = $sc['color'];
		}

		// Preparar Gráfica Proyecto
		arsort($projectCounts);
		$palette = [
			'#321fdb', '#2eb85c', '#3399ff', '#f9b115', '#e55353',
			'#6f42c1', '#20c997', '#fd7e14', '#0dcaf0', '#6c757d'
		];
		$projLabels = array_keys($projectCounts);
		$projData = array_values($projectCounts);
		$projColors = [];
		foreach ($projLabels as $idx => $label) {
			$projColors[] = $palette[$idx % count($palette)];
		}

		// Preparar Gráfica Categoría
		arsort($categoryCounts);
		$catLabels = array_keys($categoryCounts);
		$catData = array_values($categoryCounts);
		$catColors = [];
		foreach ($catLabels as $idx => $label) {
			$catColors[] = $palette[$idx % count($palette)];
		}

		return [
			'tickets'        => $tickets,
			'catalogs'       => $catalogs,
			'status_counts'  => array_values($statusCounts),
			'chart_status'   => [
				'labels' => $statusLabels,
				'data'   => $statusData,
				'colors' => $statusColors,
			],
			'chart_project'  => [
				'labels' => $projLabels,
				'data'   => $projData,
				'colors' => $projColors,
			],
			'chart_category' => [
				'labels' => $catLabels,
				'data'   => $catData,
				'colors' => $catColors,
			],
		];
	}
}
