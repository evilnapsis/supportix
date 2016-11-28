<?php
/**
* BookMedik
* @author evilnapsis
**/


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

Core::alert("Agregado exitosamente!");
Core::redir("./index.php?view=tickets");
?>