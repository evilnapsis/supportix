<?php
/**
 * @author evilnapsis
 * Supportix 2.0 (MVC + Service Layer + FastRoute + Twig)
 **/

define("ROOT", dirname(__FILE__));

$debug = true;
if ($debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

include "core/autoload.php";
include_once "core/app/autoload.php";

ob_start();
session_start();
Core::$root = "";

use App\Service\AuthService;

$routes = require __DIR__ . '/core/app/routes.php';
$dispatcher = FastRoute\simpleDispatcher($routes);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if (!empty($baseFolder) && strpos($uri, $baseFolder) === 0) {
	$uri = substr($uri, strlen($baseFolder));
}
if (empty($uri)) {
	$uri = '/';
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

if ($routeInfo[0] === FastRoute\Dispatcher::FOUND) {
	if ($httpMethod === 'POST') {
		$token = $_POST['csrf_token'] ?? null;
		if (!Session::validateCsrf($token)) {
			http_response_code(403);
			echo "<h1>403 - Token CSRF inválido</h1><p>Recarga el formulario e intenta de nuevo.</p>";
			exit;
		}
	}

	// Controladores que requieren sesión iniciada en Supportix
	$protectedControllers = [
		App\Controller\HomeController::class,
		App\Controller\TicketController::class,
		App\Controller\ProjectController::class,
		App\Controller\CategoryController::class,
		App\Controller\ReportController::class,
		App\Controller\UserController::class,
		App\Controller\ProfileController::class,
	];

	$handler = $routeInfo[1];
	if (in_array($handler[0], $protectedControllers, true) && !AuthService::check()) {
		header('Location: ' . ($baseFolder ?: '') . '/login');
		exit;
	}

	[$controllerClass, $method] = $handler;
	$controller = new $controllerClass();
	$controller->$method($routeInfo[2]);
	exit;
}

// Ruta no encontrada (404)
http_response_code(404);
echo "<!DOCTYPE html><html><head><title>404 No Encontrado</title><link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'></head><body class='bg-light d-flex align-items-center min-vh-100'><div class='container text-center'><h1 class='display-1 fw-bold text-primary'>404</h1><p class='fs-3'>Página no encontrada.</p><p class='lead'>La ruta solicitada no existe en Supportix.</p><a href='" . ($baseFolder ?: '/') . "' class='btn btn-primary'>Ir al Inicio</a></div></body></html>";
exit;
