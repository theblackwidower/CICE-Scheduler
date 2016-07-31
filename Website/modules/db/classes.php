<?php
/*
get_classes:
course_code: course code to look up
semester_id: Semester id
returns all classes (CRN) connected to course code in associative array
*/
function get_classes($course_code, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_rn, first_name, last_name FROM tbl_classes, tbl_professors WHERE
		tbl_classes.professor_id = tbl_professors.professor_id AND course_code = :course AND
		semester_id = :semester ORDER BY course_code, course_rn');
	$stmt->bindValue(':course', $course_code);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
class_rn_exists:
course_rn: CRN, course registration number
semester_id: Semester id
returns true if class exists in database
*/
function class_rn_exists($course_rn, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_rn FROM tbl_classes WHERE course_rn = :crn AND semester_id = :semester');
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':semester', $semester_id);
	return execute_exists($stmt);
}

/*
search_class_rn:
search: term to search for
semester_id: Semester id
max_results: maximum results to return
searches for classes by crn
*/
function search_class_rn($search, $semester_id, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_rn, course_code FROM tbl_classes WHERE
		course_rn ILIKE :search AND semester_id = :semester ORDER BY course_code, course_rn LIMIT :max_results');
	$stmt->bindValue(':search', $search.'%'); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_class_rn:
course_rn: CRN, course registration number
semester_id: Semester id
returns specified class in associative array
*/
function get_class_rn($course_rn, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_code, professor_id FROM tbl_classes WHERE
		course_rn = :crn AND semester_id = :semester');
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_one($stmt);
}

/*
get_class_schedule:
course_rn: CRN, course registration number
semester_id: Semester id
returns the full class schedule in associative array
*/
function get_class_schedule($course_rn, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare(
			'SELECT day_id, start_time, end_time, room_number FROM
				tbl_class_times WHERE course_rn = :crn AND semester_id = :semester');
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
add_class_rn:
semester_id: Semester id
course_rn: CRN, course registration number
course_code: course code
professor_id: id of professor teaching class
adds new class to database
returns true if successful
*/
function add_class_rn($semester_id, $course_rn, $course_code, $professor_id)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_classes(semester_id, course_rn, course_code, professor_id) VALUES (:semester, :crn, :course, :professor)');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':course', $course_code);
	$stmt->bindValue(':professor', $professor_id);
	return execute_no_data($stmt);
}

/*
update_class_rn:
semester_id: Semester id (cannot be changed)
course_rn: CRN, course registration number (cannot be changed)
professor_id: id of professor teaching class
updates class record
returns true if successful
*/
function update_class_rn($semester_id, $course_rn, $professor_id)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_classes SET professor_id = :professor WHERE course_rn = :crn AND semester_id = :semester');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	//$stmt->bindValue(':course', $course_code);
	$stmt->bindValue(':professor', $professor_id);
	return execute_no_data($stmt);
}

/*
is_room_booked:
room_number: room number
day_id: Id of day
start_time: Time class starts (24h)
end_time: Time class ends (24h)
semester_id: Semester id
returns crn if room is booked during the specified time
*/
function is_room_booked($room_number, $day_id, $start_time, $end_time, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_rn FROM tbl_class_times
		WHERE room_number = :room AND day_id = :day AND semester_id = :semester AND
			end_time > :start AND start_time < :end');
	$stmt->bindValue(':room', $room_number);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':end', $end_time);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_param($stmt, 'course_rn');
}

/*
is_prof_booked:
professor_id: id of professor teaching class
day_id: Id of day
start_time: Time class starts (24h)
end_time: Time class ends (24h)
semester_id: Semester id
returns crn if professor is booked during the specified time
*/
function is_prof_booked($professor_id, $day_id, $start_time, $end_time, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_rn FROM tbl_class_times
		WHERE day_id = :day AND semester_id = :semester AND
			end_time > :start AND start_time < :end AND
			course_rn IN (SELECT tbl_classes.course_rn FROM tbl_classes WHERE
				tbl_classes.semester_id = :semester AND tbl_classes.professor_id = :professor)');
	$stmt->bindValue(':professor', $professor_id);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':end', $end_time);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_param($stmt, 'course_rn');
}

/*
find_prof_conflicts:
semester_id: Semester id
course_rn: CRN, course registration number
professor_id: id of professor teaching class
returns crn if professor is booked during the indicated class
*/
function find_prof_conflicts($semester_id, $course_rn, $professor_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT day_id, start_time, end_time FROM tbl_classes, tbl_class_times WHERE
		tbl_classes.course_rn = tbl_class_times.course_rn AND
		tbl_classes.semester_id = tbl_class_times.semester_id AND
		tbl_classes.semester_id = :semester AND tbl_classes.course_rn = :crn');
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':semester', $semester_id);

	$looper = execute_fetch_all($stmt);

	$stmt = $conn->prepare('SELECT course_rn FROM tbl_class_times
		WHERE semester_id = :semester AND day_id = :day AND
			end_time > :start AND start_time < :end AND
			course_rn IN (SELECT tbl_classes.course_rn FROM tbl_classes WHERE
				tbl_classes.semester_id = :semester AND tbl_classes.professor_id = :professor)');
	$stmt->bindValue(':professor', $professor_id);
	$stmt->bindValue(':semester', $semester_id);
	foreach ($looper as $class_time)
	{
		$stmt->bindValue(':day', $class_time['day_id']);
		$stmt->bindValue(':start', $class_time['start_time']);
		$stmt->bindValue(':end', $class_time['end_time']);
		$result = execute_fetch_param($stmt, 'course_rn');
		if ($result !== false)
			return $result;
	}
	return false;
}

/*
is_class_booked:
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
end_time: Time class ends (24h)
semester_id: Semester id
returns room number if class is busy during the specified time.
*/
function is_class_booked($course_rn, $day_id, $start_time, $end_time, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT room_number FROM tbl_class_times
		WHERE course_rn = :crn AND day_id = :day AND semester_id = :semester AND
			end_time > :start AND start_time < :end');
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':end', $end_time);
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	return execute_fetch_param($stmt, 'room_number');
}

/*
add_class_time:
room_number: room number
day_id: Id of day
start_time: Time class starts (24h)
end_time: Time class ends (24h)
semester_id: Semester id
course_rn: CRN, course registration number
adds new class time to database
returns true if successful
*/
function add_class_time($room_number, $day_id, $start_time, $end_time, $semester_id, $course_rn)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_class_times(room_number, day_id, start_time, end_time, semester_id, course_rn) VALUES (:room, :day, :start, :end, :semester, :crn)');
	$stmt->bindValue(':room', $room_number);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':end', $end_time);
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	return execute_no_data($stmt);
}

/*
delete_class_time:
room_number: room number
day_id: Id of day
start_time: Time class starts (24h)
end_time: Time class ends (24h)
semester_id: Semester id
course_rn: CRN, course registration number
remove class time from database
returns true if successful
*/
function delete_class_time($room_number, $day_id, $start_time, $end_time, $semester_id, $course_rn)
{
	global $conn;
	$stmt = $conn->prepare('DELETE FROM tbl_class_times WHERE room_number = :room AND day_id = :day AND start_time = :start AND end_time = :end AND semester_id = :semester AND course_rn = :crn');
	$stmt->bindValue(':room', $room_number);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':end', $end_time);
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	return execute_no_data($stmt);
}

/*
get_class_time_by_room:
room_number: room number
day_id: Id of day
start_time: Time class starts (24h)
semester_id: Semester id
returns specified class time in associative array
*/
function get_class_time_by_room($room_number, $day_id, $start_time, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT end_time, course_rn FROM tbl_class_times WHERE room_number = :room AND day_id = :day AND start_time = :start AND semester_id = :semester');
	$stmt->bindValue(':room', $room_number);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_one($stmt);
}

/*
get_class_time_by_crn:
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
semester_id: Semester id
returns specified class time in associative array
*/
function get_class_time_by_crn($course_rn, $day_id, $start_time, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT end_time, room_number FROM tbl_class_times WHERE course_rn = :crn AND day_id = :day AND start_time = :start AND semester_id = :semester');
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_one($stmt);
}
