<?php
/**
* @author evilnapsis
**/
$user = TicketData::getById($_GET["id"]);
$user->del();
print "<script>window.location='index.php?view=tickets';</script>";

?>