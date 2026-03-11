<?php
if(isset($_GET["opt"]) && $_GET["opt"]=="add"){
	$r = new TicketData();
	$r->title = $_POST["title"];
	$r->description = $_POST["description"];
	$r->category_id = $_POST["category_id"];
	$r->project_id = $_POST["project_id"];
	$r->priority_id = $_POST["priority_id"];
	$r->user_id = $_SESSION["user_id"];
	$r->status_id = $_POST["status_id"];
	$r->kind_id = $_POST["kind_id"];
	$r->add();

	Core::alert("Ticket agregado exitosamente!");
	Core::redir("./index.php?view=tickets&opt=all");
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="upd"){
	if(count($_POST)>0){
		$ticket = TicketData::getById($_POST["id"]);
		$ticket->title = $_POST["title"];
		$ticket->category_id = $_POST["category_id"];
		$ticket->project_id = $_POST["project_id"];
		$ticket->priority_id = $_POST["priority_id"];
		$ticket->description = $_POST["description"];
		$ticket->status_id = $_POST["status_id"];
		$ticket->kind_id = $_POST["kind_id"];
		$ticket->update();

		Core::alert("Ticket actualizado exitosamente!");
		Core::redir("./index.php?view=tickets&opt=all");
	}
}
else if(isset($_GET["opt"]) && $_GET["opt"]=="del"){
	$ticket = TicketData::getById($_GET["id"]);
	$ticket->del();
	Core::alert("Ticket eliminado!");
	Core::redir("./index.php?view=tickets&opt=all");
}

?>
