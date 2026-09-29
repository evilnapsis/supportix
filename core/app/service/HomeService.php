<?php
namespace App\Service;

use TicketData;
use ProjectData;
use CategoryData;
use UserData;

/**
 * Servicio de métricas y resumen del Dashboard en Supportix.
 */
class HomeService {

	public static function getMetrics(): array {
		return [
			'pending_tickets'  => TicketData::countPendings(),
			'total_projects'   => count(ProjectData::getAll()),
			'total_categories' => count(CategoryData::getAll()),
			'total_users'      => count(UserData::getAll()),
		];
	}

	public static function getDashboardData(): array {
		// 1. Tarjetas numéricas
		$metrics = self::getMetrics();

		// 2. Gráfica de tickets de los últimos 30 días
		$dailyCounts = TicketData::getDailyCounts(30);
		$daysLabels = [];
		$daysData = [];
		$months = [
			1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
			7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
		];
		for ($i = 29; $i >= 0; $i--) {
			$timestamp = strtotime("-$i days");
			$dateKey = date('Y-m-d', $timestamp);
			$dayNum = date('j', $timestamp);
			$monthNum = (int)date('n', $timestamp);
			$daysLabels[] = $dayNum . ' ' . $months[$monthNum];
			$daysData[] = (int)($dailyCounts[$dateKey] ?? 0);
		}

		// 3. Gráfica de tickets por estado
		$statusCounts = TicketData::getCountsByStatus();
		$statusLabels = [];
		$statusData = [];
		$statusColors = [];
		$defaultStatusColors = [
			1 => '#f9b115', // Pendiente (Warning)
			2 => '#321fdb', // En Desarrollo (Primary)
			3 => '#2eb85c', // Terminado (Success)
			4 => '#e55353', // Cancelado (Danger)
		];
		foreach ($statusCounts as $sc) {
			$statusLabels[] = $sc['name'];
			$statusData[] = (int)$sc['total'];
			$statusColors[] = $defaultStatusColors[$sc['id']] ?? '#6c757d';
		}

		// 4. Gráfica de tickets por categoría
		$catCounts = TicketData::getCountsByCategory();
		$catLabels = [];
		$catData = [];
		$palette = [
			'#321fdb', '#2eb85c', '#3399ff', '#f9b115', '#e55353',
			'#6f42c1', '#20c997', '#fd7e14', '#0dcaf0', '#6c757d'
		];
		$catColors = [];
		foreach ($catCounts as $idx => $cc) {
			$catLabels[] = $cc['name'];
			$catData[] = (int)$cc['total'];
			$catColors[] = $palette[$idx % count($palette)];
		}

		// 5. Últimos 10 tickets
		$recentTickets = TicketData::getLatest(10);

		return [
			'metrics'        => $metrics,
			'chart_days'     => [
				'labels' => $daysLabels,
				'data'   => $daysData,
			],
			'chart_status'   => [
				'labels' => $statusLabels,
				'data'   => $statusData,
				'colors' => $statusColors,
			],
			'chart_category' => [
				'labels' => $catLabels,
				'data'   => $catData,
				'colors' => $catColors,
			],
			'recent_tickets' => $recentTickets,
		];
	}
}
