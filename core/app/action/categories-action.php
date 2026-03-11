<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
	if(count($_POST)>0){
		$category = new CategoryData();
		$category->name = $_POST["name"];
		$category->add();

		Core::alert("Categoria agregada!");
		Core::redir("./index.php?view=categories&opt=all");
	}
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	if(count($_POST)>0){
		$category = CategoryData::getById($_POST["category_id"]);
		$category->name = $_POST["name"];
		$category->update();

		Core::alert("Categoria actualizada!");
		Core::redir("./index.php?view=categories&opt=all");
	}
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
	$category = CategoryData::getById($_GET["id"]);
	$category->del();
	Core::alert("Categoria eliminada!");
	Core::redir("./index.php?view=categories&opt=all");
}

?>
