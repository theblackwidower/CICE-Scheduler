<?php
/*
get_all_professors:
max_results: maximum results to return
returns all active professors in associative array
*/
function get_all_professors($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT professor_id, first_name, last_name, email
			FROM tbl_professors WHERE is_active
			ORDER BY last_name, first_name, professor_id LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_inactive_professors:
max_results: maximum results to return
returns all inactive professors in associative array
*/
function get_inactive_professors($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT professor_id, first_name, last_name, email
			FROM tbl_professors WHERE NOT is_active
			ORDER BY last_name, first_name, professor_id LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_inactive_professors:
search: term to search for
max_results: maximum results to return
searches for inactive professors by name
*/
function search_inactive_professors($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT professor_id, first_name, last_name, email
			FROM tbl_professors WHERE NOT is_active AND '.SQL_NAME_SEARCH.
			' ORDER BY last_name, first_name, professor_id LIMIT :max_results');
	$stmt->bindValue(':search', $search.'%'); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_professors:
search: term to search for
max_results: maximum results to return
searches for active professors by name
*/
function search_professors($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT professor_id, first_name, last_name, email
			FROM tbl_professors WHERE is_active AND '.SQL_NAME_SEARCH.
			' ORDER BY last_name, first_name, professor_id LIMIT :max_results');
	$stmt->bindValue(':search', $search.'%'); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
professor_exists:
professor_id: professor's unique id
returns true if professor exists in database
*/
function professor_exists($professor_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT professor_id
			FROM tbl_professors WHERE professor_id = :id');
	$stmt->bindValue(':id', $professor_id);
	return execute_exists($stmt);
}

/*
professor_email_exists:
email: professor's email
returns true if professor's email is used in database
*/
function professor_email_exists($email)
{
	global $conn;
	$stmt = $conn->prepare('SELECT professor_id
			FROM tbl_professors WHERE email = :email');
	$stmt->bindValue(':email', $email);
	return execute_exists($stmt);
}

/*
get_professor_name:
professor_id: professor's unique id
name_format: code for name format
returns name of professor in specified format
*/
function get_professor_name($professor_id, $name_format = NAME_FORMAT_LAST_NAME_FIRST)
{
	global $conn;
	$stmt = $conn->prepare('SELECT first_name, last_name
			FROM tbl_professors WHERE professor_id = :id');
	$stmt->bindValue(':id', $professor_id);
	return execute_fetch_name($stmt, $name_format);
}

/*
add_professor:
first_name: professor's first name
last_name: professor's last name
email: professor's email
adds new professor to database
returns true if successful
*/
function add_professor($first_name, $last_name, $email)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_professors (email, first_name, last_name)
			VALUES (:email, :first_name, :last_name) RETURNING professor_id');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':first_name', $first_name);
	$stmt->bindValue(':last_name', $last_name);
	return execute_fetch_param($stmt, 'professor_id');
}

/*
get_professor:
professor_id: professor's unique id
returns specified professor in associative array
*/
function get_professor($professor_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT professor_id, first_name, last_name, email, is_active
			FROM tbl_professors WHERE professor_id = :id');
	$stmt->bindValue(':id', $professor_id);
	return execute_fetch_one($stmt);
}

/*
update_professor:
professor_id: professor's unique id (cannot be changed)
first_name: professor's new first name
last_name: professor's new last name
email: professor's email
is_active: is record active
updates professor record
returns true if successful
*/
function update_professor($professor_id, $first_name, $last_name, $email, $is_active)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_professors
			SET first_name = :first_name, last_name = :last_name, email = :email, is_active = :is_active WHERE professor_id = :id');
	$stmt->bindValue(':id', $professor_id);
	$stmt->bindValue(':first_name', $first_name);
	$stmt->bindValue(':last_name', $last_name);
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':is_active', $is_active);
	return execute_no_data($stmt);
}
