<?php
namespace App\Controller;

use App\Service\CategoryService;
use ViewEngine;
use Req;

/**
 * Gestiona el catálogo de categorías en Supportix.
 */
class CategoryController {
	private string $baseFolder;

	public function __construct() {
		$this->baseFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
	}

	public function index(): void {
		$categories = CategoryService::getAll();
		ViewEngine::render('categories/index.html.twig', [
			'categories' => $categories
		]);
	}

	public function new(): void {
		ViewEngine::render('categories/new.html.twig');
	}

	public function create(): void {
		$res = CategoryService::create($_POST);
		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/category/new');
			exit;
		}

		$_SESSION['success'] = '¡Categoría creada correctamente!';
		header('Location: ' . $this->baseFolder . '/categories');
		exit;
	}

	public function edit(array $params): void {
		$id = (int)($params['id'] ?? 0);
		$category = CategoryService::getById($id);
		if (!$category) {
			$_SESSION['error'] = 'La categoría no existe.';
			header('Location: ' . $this->baseFolder . '/categories');
			exit;
		}

		ViewEngine::render('categories/edit.html.twig', [
			'category' => $category
		]);
	}

	public function update(array $params): void {
		$id = (int)($params['id'] ?? 0);
		$res = CategoryService::update($id, $_POST);

		if (!$res['success']) {
			$_SESSION['error'] = $res['error'];
			header('Location: ' . $this->baseFolder . '/category/' . $id . '/edit');
			exit;
		}

		$_SESSION['updated'] = '¡Categoría actualizada correctamente!';
		header('Location: ' . $this->baseFolder . '/categories');
		exit;
	}

	public function delete(array $params): void {
		$id = (int)($params['id'] ?? 0);
		CategoryService::delete($id);

		$_SESSION['deleted'] = '¡Categoría eliminada correctamente!';
		header('Location: ' . $this->baseFolder . '/categories');
		exit;
	}
}
