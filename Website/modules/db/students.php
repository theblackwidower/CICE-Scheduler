<?php
/*
get_all_registered_students:
semester_id: Semester id
max_results: maximum results to return
returns all active registered students in associative array
*/
function get_all_registered_students($semester_id, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT DISTINCT tbl_students.student_id, first_name, last_name FROM
		tbl_students, tbl_student_classes WHERE semester_id = :semester AND
		tbl_students.student_id = tbl_student_classes.student_id AND is_active
		ORDER BY last_name, first_name, student_id LIMIT :max_results');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_registered_students:
semester_id: Semester id
search: term to search for
max_results: maximum results to return
searches for active registered students by name
*/
function search_registered_students($semester_id, $search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare("SELECT DISTINCT tbl_students.student_id, first_name, last_name FROM
		tbl_students, tbl_student_classes WHERE semester_id = :semester AND
		tbl_students.student_id = tbl_student_classes.student_id AND
		(".SQL_NAME_SEARCH." OR tbl_students.student_id ILIKE :search || '%') AND is_active
		ORDER BY last_name, first_name, student_id LIMIT :max_results");
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':search', $search); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_all_unregistered_students:
semester_id: Semester id
max_results: maximum results to return
returns all active unregistered students in associative array
*/
function get_all_unregistered_students($semester_id, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT student_id, first_name, last_name FROM
		tbl_students WHERE student_id NOT IN
		(SELECT student_id FROM tbl_student_classes WHERE semester_id = :semester) AND
		is_active ORDER BY last_name, first_name, student_id LIMIT :max_results');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_unregistered_students:
semester_id: Semester id
search: term to search for
max_results: maximum results to return
searches for active unregistered students by name
*/
function search_unregistered_students($semester_id, $search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare("SELECT student_id, first_name, last_name FROM
		tbl_students WHERE student_id NOT IN
		(SELECT student_id FROM tbl_student_classes WHERE semester_id = :semester) AND
		(".SQL_NAME_SEARCH." OR student_id ILIKE :search || '%') AND is_active
		ORDER BY last_name, first_name, student_id LIMIT :max_results");
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':search', $search); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_students:
search: term to search for
max_results: maximum results to return
searches for active students by name
*/
function search_students($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare("SELECT student_id, first_name, last_name FROM
		tbl_students WHERE (".SQL_NAME_SEARCH." OR student_id ILIKE :search || '%') AND is_active
		ORDER BY last_name, first_name, student_id LIMIT :max_results");
	$stmt->bindValue(':search', $search); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_inactive_students:
search: term to search for
max_results: maximum results to return
searches for inactive students by name
*/
function search_inactive_students($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare("SELECT student_id, first_name, last_name FROM
		tbl_students WHERE (".SQL_NAME_SEARCH." OR student_id ILIKE :search || '%') AND NOT is_active
		ORDER BY last_name, first_name, student_id LIMIT :max_results");
	$stmt->bindValue(':search', $search); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_all_students:
max_results: maximum results to return
returns all active students in associative array
*/
function get_all_students($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT student_id, first_name, last_name FROM
		tbl_students WHERE is_active ORDER BY last_name, first_name, student_id LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_inactive_students:
max_results: maximum results to return
returns all inactive students in associative array
*/
function get_inactive_students($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT student_id, first_name, last_name FROM
		tbl_students WHERE NOT is_active ORDER BY last_name, first_name, student_id LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_student_name:
student_id: Student's unique banner id
name_format: code for name format
returns name of student in specified format
*/
function get_student_name($student_id, $name_format = NAME_FORMAT_LAST_NAME_FIRST)
{
	global $conn;
	$stmt = $conn->prepare('SELECT first_name, last_name FROM tbl_students WHERE student_id = :id');
	$stmt->bindValue(':id', $student_id);
	return execute_fetch_name($stmt, $name_format);
}

/*
get_student_schedule:
student_id: Student's unique banner id
semester_id: Semester id
returns the full student schedule in associative array
*/
function get_student_schedule($student_id, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare(
			'SELECT day_id, start_time, end_time, room_number, course_code,
			tbl_class_times.course_rn, professor_id FROM
			tbl_student_classes, tbl_classes, tbl_class_times WHERE
			student_id = :id AND tbl_student_classes.semester_id = :semester AND
			tbl_classes.semester_id = tbl_class_times.semester_id AND
			tbl_class_times.semester_id = tbl_student_classes.semester_id AND
			tbl_classes.course_rn = tbl_class_times.course_rn AND
			tbl_class_times.course_rn = tbl_student_classes.course_rn');
	$stmt->bindValue(':id', $student_id);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
get_student_schedule:
student_id: Student's unique banner id
semester_id: Semester id
returns all classes (CRN) student registered for in associative array
*/
function get_student_registration($student_id, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare(
			'SELECT tbl_classes.course_code, tbl_student_classes.course_rn, course_name, professor_id FROM
			tbl_student_classes, tbl_classes, tbl_courses WHERE
			student_id = :id AND tbl_student_classes.semester_id = :semester AND
			tbl_student_classes.semester_id = tbl_classes.semester_id AND
			tbl_student_classes.course_rn = tbl_classes.course_rn AND
			tbl_classes.course_code = tbl_courses.course_code');
	$stmt->bindValue(':id', $student_id);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
student_registration_exists:
student_id: Student's unique banner id
semester_id: Semester id
course_rn: CRN, course registration number
returns true if student is registered to class (CRN)
*/
function student_registration_exists($student_id, $semester_id, $course_rn)
{
	global $conn;
	$stmt = $conn->prepare('SELECT student_id FROM tbl_student_classes WHERE student_id = :student AND semester_id = :semester AND course_rn = :course');
	$stmt->bindValue(':student', $student_id);
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':course', $course_rn);
	return execute_exists($stmt);
}

/*
add_student_registration:
student_id: Student's unique banner id
semester_id: Semester id
course_rn: CRN, course registration number
add student to class
returns true if successful
*/
function add_student_registration($student_id, $semester_id, $course_rn)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_student_classes(student_id, semester_id, course_rn) VALUES (:student, :semester, :course)');
	$stmt->bindValue(':student', $student_id);
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':course', $course_rn);
	return execute_no_data($stmt);
}

/*
delete_student_registration:
student_id: Student's unique banner id
semester_id: Semester id
course_rn: CRN, course registration number
remove student from class
returns true if successful
*/
function delete_student_registration($student_id, $semester_id, $course_rn)
{
	global $conn;
	$stmt = $conn->prepare('DELETE FROM tbl_student_classes WHERE student_id = :student AND semester_id = :semester AND course_rn = :course');
	$stmt->bindValue(':student', $student_id);
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':course', $course_rn);
	return execute_no_data($stmt);
}

/*
student_exists:
student_id: Student's unique banner id
returns true if student exists in database
*/
function student_exists($student_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT student_id FROM tbl_students WHERE student_id = :id');
	$stmt->bindValue(':id', $student_id);
	return execute_exists($stmt);
}

/*
add_student:
student_id: Student's unique banner id
first_name: student's first name
last_name: student's last name
adds student to database
returns true if successful
*/
function add_student($student_id, $first_name, $last_name)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_students(student_id, first_name, last_name) VALUES (:id, :first_name, :last_name)');
	$stmt->bindValue(':id', $student_id);
	$stmt->bindValue(':first_name', $first_name);
	$stmt->bindValue(':last_name', $last_name);
	return execute_no_data($stmt);
}

/*
get_student:
student_id: Student's unique banner id
returns specified student in associative array
*/
function get_student($student_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT first_name, last_name, is_active FROM tbl_students WHERE student_id = :id');
	$stmt->bindValue(':id', $student_id);
	return execute_fetch_one($stmt);
}

/*
update_student:
student_id: Student's unique banner id
first_name: student's first name
last_name: student's last name
is_active: is record active
updates student record
returns true if successful
*/
function update_student($student_id, $first_name, $last_name, $is_active)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_students SET first_name = :first_name, last_name = :last_name, is_active = :is_active WHERE student_id = :id');
	$stmt->bindValue(':id', $student_id);
	$stmt->bindValue(':first_name', $first_name);
	$stmt->bindValue(':last_name', $last_name);
	$stmt->bindValue(':is_active', $is_active);
	return execute_no_data($stmt);
}
