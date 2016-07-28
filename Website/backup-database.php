<?php
require_once "modules/constants.php";
$restrictionCode = ROLE_ADMIN;
require_once "modules/init.php";

switch ($_GET['t'])
{
	case 'schema':
		$filename = 'schema-cice-scheduler-backup';
		$options = '-sc --if-exists';
		break;

	case 'full':
		$filename = 'full-cice-scheduler-backup';
		$options = '-c --if-exists';
		break;

	case 'data':
		$filename = 'cice-scheduler-backup';
		$options = '-a';
		break;

	default:
		exit;
}

//header("Content-Type: text/sql");
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$filename.'_'.date('Y-m-d_H.i.s').'.sql"');

passthru('pg_dump '.$options.' '.DATABASE_NAME);
