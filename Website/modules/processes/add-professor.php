<?php
$first_name = trim($_POST["first_name"]);
$last_name = trim($_POST["last_name"]);
$email = trim($_POST["email"]);

$result = "";

if ($first_name == "")
	$result .= "Please enter a first name.<br />";

if ($last_name == "")
	$result .= "Please enter a last name.<br />";

if ($email == "")
	$email = null;
else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	$result .= '<em>"'.$email.'"</em> is not a valid email address.<br />';
else if (professor_email_exists($email))
	$result .= '<em>"'.$email.'"</em> is already registered to a professor.<br />';

if ($result == "")
{
	$professor_id = add_professor($first_name, $last_name, $email);
	if ($professor_id !== false)
		$result = true;
	else
		$result = "Unknown error occured.";
}
