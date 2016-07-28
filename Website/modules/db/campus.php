<?php
/*
get_all_campuses:
returns all campuses in associative array
*/
function get_all_campuses()
{
	global $conn;
	$stmt = $conn->prepare('SELECT campus_id, campus_name FROM tbl_campuses');
	return execute_fetch_all($stmt);
}

/*
campus_exists:
campus_id: Id of campus to check
returns true if campus id is valid
*/
function campus_exists($campus_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT campus_id FROM tbl_campuses WHERE campus_id = :id');
	$stmt->bindValue(':id', $campus_id);
	return execute_exists($stmt);
}

/*
add_campus:
campus_id: Id of campus
campus_name: Name of campus
add campus to database
returns true if successful
*/
function add_campus($campus_id, $campus_name)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_campuses (campus_id, campus_name) VALUES (:id, :name)');
	$stmt->bindValue(':id', $campus_id);
	$stmt->bindValue(':name', $campus_name);
	return execute_no_data($stmt);
}

/*
get_campus_name_from_room:
room_number: room number to look up
returns name of campus room exists in.
*/
function get_campus_name_from_room($room_number)
{
	global $conn;
	$stmt = $conn->prepare('SELECT campus_name FROM tbl_campuses, tbl_rooms
		WHERE room_number = :room AND tbl_campuses.campus_id = tbl_rooms.campus_id');
	$stmt->bindValue(':room', $room_number);
	return execute_fetch_param($stmt, 'campus_name');
}
