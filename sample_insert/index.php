<?php
session_start();
$root = $_SESSION['link_root'];
if(!isset($_SESSION['username'])){
	$url = $_SERVER["HTTP_HOST"].$root."login.php"; 
	header("Location: http://".$url);
	exit();
}
?>