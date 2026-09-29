<?php
use App\Controller\HomeController;
use App\Controller\AuthController;
use App\Controller\TicketController;
use App\Controller\ProjectController;
use App\Controller\CategoryController;
use App\Controller\ReportController;
use App\Controller\UserController;
use App\Controller\ProfileController;

return function(FastRoute\RouteCollector $r) {
	// Dashboard / Inicio
	$r->addRoute('GET', '/', [HomeController::class, 'index']);
	$r->addRoute('GET', '/home', [HomeController::class, 'index']);

	// Autenticación
	$r->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
	$r->addRoute('POST', '/login', [AuthController::class, 'processLogin']);
	$r->addRoute('GET', '/logout', [AuthController::class, 'logout']);

	// Tickets
	$r->addRoute('GET', '/tickets', [TicketController::class, 'index']);
	$r->addRoute('GET', '/ticket/new', [TicketController::class, 'new']);
	$r->addRoute('POST', '/ticket/create', [TicketController::class, 'create']);
	$r->addRoute('GET', '/ticket/{id:\d+}/edit', [TicketController::class, 'edit']);
	$r->addRoute('POST', '/ticket/{id:\d+}/update', [TicketController::class, 'update']);
	$r->addRoute('POST', '/ticket/{id:\d+}/delete', [TicketController::class, 'delete']);

	// Proyectos
	$r->addRoute('GET', '/projects', [ProjectController::class, 'index']);
	$r->addRoute('GET', '/project/new', [ProjectController::class, 'new']);
	$r->addRoute('POST', '/project/create', [ProjectController::class, 'create']);
	$r->addRoute('GET', '/project/{id:\d+}/tickets', [ProjectController::class, 'tickets']);
	$r->addRoute('GET', '/project/{id:\d+}/edit', [ProjectController::class, 'edit']);
	$r->addRoute('POST', '/project/{id:\d+}/update', [ProjectController::class, 'update']);
	$r->addRoute('POST', '/project/{id:\d+}/delete', [ProjectController::class, 'delete']);

	// Categorías
	$r->addRoute('GET', '/categories', [CategoryController::class, 'index']);
	$r->addRoute('GET', '/category/new', [CategoryController::class, 'new']);
	$r->addRoute('POST', '/category/create', [CategoryController::class, 'create']);
	$r->addRoute('GET', '/category/{id:\d+}/edit', [CategoryController::class, 'edit']);
	$r->addRoute('POST', '/category/{id:\d+}/update', [CategoryController::class, 'update']);
	$r->addRoute('POST', '/category/{id:\d+}/delete', [CategoryController::class, 'delete']);

	// Reportes
	$r->addRoute('GET', '/reports', [ReportController::class, 'index']);

	// Usuarios
	$r->addRoute('GET', '/users', [UserController::class, 'index']);
	$r->addRoute('GET', '/user/new', [UserController::class, 'new']);
	$r->addRoute('POST', '/user/create', [UserController::class, 'create']);
	$r->addRoute('GET', '/user/{id:\d+}/edit', [UserController::class, 'edit']);
	$r->addRoute('POST', '/user/{id:\d+}/update', [UserController::class, 'update']);
	$r->addRoute('POST', '/user/{id:\d+}/delete', [UserController::class, 'delete']);

	// Perfil
	$r->addRoute('GET', '/profile', [ProfileController::class, 'index']);
	$r->addRoute('POST', '/profile/change-password', [ProfileController::class, 'changePassword']);
};
