<?php
if (!isset($_SESSION))
	session_start();
session_unset();
session_destroy();
session_start();
$_SESSION['message'] = 'Successfully logged out.';
header("Location: login.php");
