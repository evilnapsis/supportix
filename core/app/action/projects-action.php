<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
	if(count($_POST)>0){
		$project = new ProjectData();
		$project->name = $_POST["name"];
		$project->add();

		Core::alert("Proyecto agregado!");
		Core::redir("./index.php?view=projects&opt=all");
	}
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	if(count($_POST)>0){
		$project = ProjectData::getById($_POST["project_id"]);
		$project->name = $_POST["name"];
		$project->update();

		Core::alert("Proyecto actualizado!");
		Core::redir("./index.php?view=projects&opt=all");
	}
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
	$project = ProjectData::getById($_GET["id"]);
	$project->del();
	Core::alert("Proyecto eliminado!");
	Core::redir("./index.php?view=projects&opt=all");
}

?>
