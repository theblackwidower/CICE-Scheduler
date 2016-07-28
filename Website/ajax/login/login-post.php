<?php
require_once "../../modules/constants.php";
$restrictionCode = PUBLIC_ACCESS;
require_once "../../modules/init.php";
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	require '../../modules/processes/login.php';

	$output = Array();

	if ($result === true)
		$output["login_successful"] = true;
	else
	{
		$output["message"] = $result;
		$output["login_successful"] = false;
	}

	echo json_encode($output);
}
