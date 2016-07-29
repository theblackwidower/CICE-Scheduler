<?php
require_once "../../modules/constants.php";
$restrictionCode = PUBLIC_ACCESS;
require_once "../../modules/init.php";
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	require '../../modules/processes/login.php';

	$output = Array();

	if ($result === 'logged in')
		$output["login_successful"] = true;
	else if ($result === 'change password')
		$output["message"] = 'Your password needs to be changed.<br />
			<a href="'.SITE_FOLDER.'user-change-password.php" target="_blank">Click here to change your password.</a>';
		$output["login_successful"] = false;
	else
	{
		$output["message"] = $result;
		$output["login_successful"] = false;
	}

	echo json_encode($output);
}
