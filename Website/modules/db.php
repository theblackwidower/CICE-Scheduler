<?php
require_once "dblogin.php";
try
{
	$conn = connect();
	require_once "db/campus.php";
	require_once "db/classes.php";
	require_once "db/courses.php";
	require_once "db/days.php";
	require_once "db/facilitators.php";
	require_once "db/professors.php";
	require_once "db/rooms.php";
	require_once "db/schedules.php";
	require_once "db/security.php";
	require_once "db/semesters.php";
	require_once "db/students.php";
}
catch (PDOException $e)
{
	header("HTTP/1.0 503 Service Unavailable");
	include '503.php';
	//header("Location: 503.php");
	//close php document
	exit;
}

/*
execute_exists:
stmt: prepared statement, ready for execution
returns true if any records are found
*/
function execute_exists($stmt)
{
	$stmt->execute();
	return ($stmt->rowCount() > 0);
}

/*
execute_fetch_all:
stmt: prepared statement, ready for execution
return all records found as associated arrays
*/
function execute_fetch_all($stmt)
{
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*
execute_fetch_one:
stmt: prepared statement, ready for execution
return first record found as an associated array, only one should be expected
*/
function execute_fetch_one($stmt)
{
	$stmt->execute();
	if ($stmt->rowcount() < 1)
		return false;
	else
		return $stmt->fetch(PDO::FETCH_ASSOC);
}

/*
execute_fetch_obj:
stmt: prepared statement, ready for execution
return first record found as an object, only one should be expected
*/
function execute_fetch_obj($stmt)
{
	$stmt->execute();
	if ($stmt->rowcount() < 1)
		return false;
	else
		return $stmt->fetch(PDO::FETCH_OBJ);
}

/*
execute_fetch_param:
stmt: prepared statement, ready for execution
param: field name to fetch
return specified field from first record found, only one should be expected
*/
function execute_fetch_param($stmt, $param)
{
	$stmt->execute();
	if ($stmt->rowcount() < 1)
		return false;
	else
		return $stmt->fetch(PDO::FETCH_ASSOC)[$param];
}

/*
execute_fetch_name:
stmt: prepared statement, ready for execution
name_format: code for name format
return proper name of subject of record
*/
function execute_fetch_name($stmt, $name_format)
{
	$stmt->execute();
	if ($stmt->rowcount() > 0)
	{
		$person = $stmt->fetch(PDO::FETCH_OBJ);
		return format_name($person->first_name, $person->last_name, $name_format);
	}
	else
		return false;
}

/*
execute_no_data:
stmt: prepared statement, ready for execution
return true if operation was sucessful. Otherwise return error code. For updates and inserts.
*/
function execute_no_data($stmt)
{
	if ($stmt->execute())
		return true;
	else
		return $stmt->errorCode();
}
