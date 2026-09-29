<?php
namespace App\Controller;

use App\Service\ReportService;
use ViewEngine;
use Req;

/**
 * Controlador para la generación y visualización de reportes de tickets en Supportix.
 */
class ReportController {

	public function index(): void {
		$filters = [
			'project_id'  => Req::get('project_id', ''),
			'category_id' => Req::get('category_id', ''),
			'priority_id' => Req::get('priority_id', ''),
			'status_id'   => Req::get('status_id', ''),
			'kind_id'     => Req::get('kind_id', ''),
			'start_at'    => Req::get('start_at', ''),
			'finish_at'   => Req::get('finish_at', ''),
		];

		$reportData = ReportService::getReportData($filters);

		ViewEngine::render('reports/index.html.twig', [
			'tickets'        => $reportData['tickets'],
			'projects'       => $reportData['catalogs']['projects'],
			'categories'     => $reportData['catalogs']['categories'],
			'priorities'     => $reportData['catalogs']['priorities'],
			'statuses'       => $reportData['catalogs']['statuses'],
			'kinds'          => $reportData['catalogs']['kinds'],
			'status_counts'  => $reportData['status_counts'],
			'chart_status'   => $reportData['chart_status'],
			'chart_project'  => $reportData['chart_project'],
			'chart_category' => $reportData['chart_category'],
			'filters'        => $filters,
		]);
	}
}
