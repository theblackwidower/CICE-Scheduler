<?php
/*
get_all_courses:
max_results: maximum results to return
returns all active courses in associative array
*/
function get_all_courses($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_code, course_name
		FROM tbl_courses WHERE is_active ORDER BY course_code LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_inactive_courses:
max_results: maximum results to return
returns all inactive courses in associative array
*/
function get_inactive_courses($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_code, course_name
		FROM tbl_courses WHERE NOT is_active ORDER BY course_code LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_inactive_courses:
search: term to search for
max_results: maximum results to return
searches for inactive courses by course code or by name
*/
function search_inactive_courses($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare("SELECT course_code, course_name FROM tbl_courses WHERE NOT is_active AND
		(REPLACE(course_code, ' ', '') ILIKE '%' || REPLACE(:search, ' ', '') || '%'
			OR course_name ILIKE :search)
			ORDER BY course_code LIMIT :max_results");
	$stmt->bindValue(':search', $search); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_courses:
search: term to search for
max_results: maximum results to return
searches for active courses by course code or by name
*/
function search_courses($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare("SELECT course_code, course_name FROM tbl_courses WHERE is_active AND
		(REPLACE(course_code, ' ', '') ILIKE '%' || REPLACE(:search, ' ', '') || '%'
			OR course_name ILIKE :search)
			ORDER BY course_code LIMIT :max_results");
	$stmt->bindValue(':search', $search); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
course_exists:
course_code: course code
returns true if course exists in database
*/
function course_exists($course_code)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_code FROM tbl_courses WHERE course_code = UPPER(:code)');
	$stmt->bindValue(':code', $course_code);
	return execute_exists($stmt);
}

/*
add_course:
course_code: course code
course_name: course name
adds new course to database
returns true if successful
*/
function add_course($course_code, $course_name)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_courses(course_code, course_name) VALUES (UPPER(:code), :name)');
	$stmt->bindValue(':code', $course_code);
	$stmt->bindValue(':name', $course_name);
	return execute_no_data($stmt);
}

/*
get_course_name:
course_code: course code
returns name of course
*/
function get_course_name($course_code)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_name FROM tbl_courses WHERE course_code = UPPER(:code)');
	$stmt->bindValue(':code', $course_code);
	return execute_fetch_param($stmt, 'course_name');
}

/*
get_course:
course_code: course code
returns specified course in associative array
*/
function get_course($course_code)
{
	global $conn;
	$stmt = $conn->prepare('SELECT course_name, is_active FROM tbl_courses WHERE course_code = UPPER(:code)');
	$stmt->bindValue(':code', $course_code);
	return execute_fetch_one($stmt);
}

/*
get_course_from_crn:
course_rn: CRN, course registration number
semester_id: Semester id
returns course referenced by crn in associative array
*/
function get_course_from_crn($course_rn, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT tbl_courses.course_code, course_name
		FROM tbl_classes, tbl_courses WHERE course_rn = :crn AND
		semester_id = :semester AND tbl_classes.course_code = tbl_courses.course_code');
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_one($stmt);
}

/*
update_course:
course_code: course code (cannot be changed)
course_name: course name
is_active: is record active
updates course record
returns true if successful
*/
function update_course($course_code, $course_name, $is_active)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_courses SET course_name = :name, is_active = :is_active
		WHERE course_code = UPPER(:code)');
	$stmt->bindValue(':code', $course_code);
	$stmt->bindValue(':name', $course_name);
	$stmt->bindValue(':is_active', $is_active);
	return execute_no_data($stmt);
}
