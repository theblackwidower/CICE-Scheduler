<?php
if (!isset($_SESSION))
	session_start();

require_once "constants.php";
require_once "functions.php";

if (!isset($restrictionCode))
{
	set_session_message("Security Error.");
	header("HTTP/1.0 401 Unauthorized");
	include '401.php';
	//close php document
	exit;
}
else if (defined($restrictionCode))
	$restrictionCode = constant($restrictionCode);//*/

if ($restrictionCode == PUBLIC_ACCESS)
	$access_denied = false;
else if (!is_logged_in())
	$access_denied = true;
else if ($restrictionCode == ALL_USERS)
	$access_denied = false;
else if (get_logged_in_role() == ROLE_ADMIN)
	$access_denied = false;
else if (get_logged_in_role() == $restrictionCode)
	$access_denied = false;
else
	$access_denied = true;

if ($access_denied)
{
	set_session_message("Access denied.");
	header("HTTP/1.0 401 Unauthorized");
	include '401.php';
	//close php document
	exit;
}

require_once "db.php";
ob_start();
