<?php
/**
* BookMedik
* @author evilnapsis
* @url http://evilnapsis.com/about/
**/

if(count($_POST)>0){
	$user = new UserData();
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->username = $_POST["username"];
	$user->email = $_POST["email"];
	$user->kind = $_POST["kind"];
	$user->password = sha1(md5($_POST["password"]));
	$user->add();

print "<script>window.location='index.php?view=users';</script>";


}


?>