<?php
$email = trim($_POST["login_email"]);
$password = trim($_POST["password"]);

if ($email == "")
	$result = "Please enter an email.";
else if ($password == "")
	$result = "Please enter a password.";
else if (!login($email, $password))
	$result = "Invalid login or password.";
else
	$result = true;
