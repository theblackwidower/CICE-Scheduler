<?php
/*
get_all_days:
return all days as associative array
*/
function get_all_days()
{
	global $conn;
	$stmt = $conn->prepare('SELECT day_id, day_name FROM tbl_days');
	return execute_fetch_all($stmt);
}

/*
is_valid_day:
day_id: Id of day to check
return true if day has entry in database.
*/
function is_valid_day($day_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT day_id FROM tbl_days WHERE day_id = :id');
	$stmt->bindValue(':id', $day_id);
	return execute_exists($stmt);
}

/*
get_day_name:
day_id: Id of day
return name of day.
*/
function get_day_name($day_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT day_name FROM tbl_days WHERE day_id = :id');
	$stmt->bindValue(':id', $day_id);
	return execute_fetch_param($stmt, 'day_name');
}
