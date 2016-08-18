<?php
/*
search_rooms:
search: term to search for
max_results: maximum results to return
searches for room by number
*/
function search_rooms($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare("SELECT room_number, campus_name FROM tbl_rooms, tbl_campuses
			WHERE room_number ILIKE '%' || REPLACE(:search, ' ', '') || '%'
			AND tbl_rooms.campus_id = tbl_campuses.campus_id
			ORDER BY tbl_rooms.campus_id, room_number LIMIT :max_results");
	$stmt->bindValue(':search', $search); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_all_rooms_on_campus:
campus_id: id of campus to look up rooms for
returns all rooms on campus in associative arrays.
*/
function get_all_rooms_on_campus($campus_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT room_number FROM tbl_rooms
			WHERE campus_id = :campus ORDER BY room_number');
	$stmt->bindValue(':campus', $campus_id);
	return execute_fetch_all($stmt);
}

/*
get_rooms_campus:
room_number: room number to look up
returns campus id where room is located
*/
function get_rooms_campus($room_number)
{
	global $conn;
	$stmt = $conn->prepare('SELECT campus_id FROM tbl_rooms WHERE room_number = :room');
	$stmt->bindValue(':room', $room_number);
	return execute_fetch_param($stmt, 'campus_id');
}

/*
room_exists:
room_number: room number to look up
returns true if room exists
*/
function room_exists($room_number)
{
	global $conn;
	$stmt = $conn->prepare('SELECT room_number FROM tbl_rooms WHERE room_number = :room');
	$stmt->bindValue(':room', $room_number);
	return execute_exists($stmt);
}

/*
add_room:
campus_id: id of campus where room is located
room_number: room number
adds new room to database
returns true if successful
*/
function add_room($campus_id, $room_number)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_rooms (campus_id, room_number) VALUES (:campus, :room)');
	$stmt->bindValue(':campus', $campus_id);
	$stmt->bindValue(':room', $room_number);
	return execute_no_data($stmt);
}
