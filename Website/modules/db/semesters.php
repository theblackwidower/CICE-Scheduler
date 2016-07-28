<?php
/*
get_current_semester:
returns semester id for current semester or (if between semesters) next semester
*/
function get_current_semester()
{
	global $conn;
	$stmt = $conn->prepare('SELECT semester_id FROM tbl_semesters
			WHERE :today < end_date ORDER BY end_date LIMIT 1');
	$stmt->bindValue(':today', date('c'));
	return execute_fetch_param($stmt, 'semester_id');
}

/*
is_registered_semester:
semester_id: semester id to look up
returns true if semester is valid
*/
function is_registered_semester($semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT semester_id FROM tbl_semesters
			WHERE semester_id = :semester');
	$stmt->bindValue(':semester', $semester_id);
	return execute_exists($stmt);
}

/*
get_all_semesters:
returns all semesters in associative arrays
*/
function get_all_semesters()
{
	global $conn;
	$stmt = $conn->prepare('SELECT semester_id FROM tbl_semesters ORDER BY start_date');
	return execute_fetch_all($stmt);
}

/*
get_semester_date:
semester_id: semester id to look up
return date span for semester as string to display
*/
function get_semester_date($semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT start_date, end_date FROM tbl_semesters
			WHERE semester_id = :semester');
	$stmt->bindValue(':semester', $semester_id);
	$data = execute_fetch_obj($stmt);
	return date_format(date_create($data->start_date), 'M j, Y').
		' to '.date_format(date_create($data->end_date), 'M j, Y');
}

/*
add_semester:
semester_id: ID for new semester
start_date: start date
end_date: end date
Add new semester to database
returns true if successful
*/
function add_semester($semester_id, $start_date, $end_date)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_semesters (semester_id, start_date, end_date)
						VALUES (:id, :start_date, :end_date)');
	$stmt->bindValue(':id', $semester_id);
	$stmt->bindValue(':start_date', $start_date);
	$stmt->bindValue(':end_date', $end_date);
	return execute_no_data($stmt);
}
