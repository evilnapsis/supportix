<?php
namespace App\Controller;

use App\Service\HomeService;
use ViewEngine;

/**
 * Controla la vista principal (Dashboard) de Supportix.
 */
class HomeController {

	public function index(): void {
		$dashboardData = HomeService::getDashboardData();
		ViewEngine::render('home/index.html.twig', $dashboardData);
	}
}
