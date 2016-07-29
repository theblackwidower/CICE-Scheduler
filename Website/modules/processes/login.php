<?php
$email = trim($_POST["login_email"]);
$password = trim($_POST["password"]);

if ($email == "")
	$result = "Please enter an email.";
else if ($password == "")
	$result = "Please enter a password.";
else
{
	$login_result = login($email, $password);
	if ($login_result === 0)
		$result = "Invalid login or password.";
	else if ($login_result === 1)
		$result = 'logged in';
	else if ($login_result === 2)
		//Change password
		$result = 'change password';
}
