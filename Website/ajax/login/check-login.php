<?php
header('Content-type: application/json');

if (!isset($_SESSION))
	session_start();

if (isset($_SESSION['login']))
	$is_logged_in = true;
else
	$is_logged_in = false;

echo json_encode(array("is_logged_in" => $is_logged_in));
