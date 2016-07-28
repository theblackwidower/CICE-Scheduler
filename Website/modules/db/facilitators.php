<?php
/*
get_all_facilitators:
max_results: maximum results to return
returns all active facilitators in associative array
*/
function get_all_facilitators($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email, first_name, last_name
			FROM tbl_facilitators WHERE is_active
			ORDER BY last_name, first_name, email LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_inactive_facilitators:
max_results: maximum results to return
returns all inactive facilitators in associative array
*/
function get_inactive_facilitators($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email, first_name, last_name
			FROM tbl_facilitators WHERE NOT is_active
			ORDER BY last_name, first_name, email LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_facilitator_name:
email: facilitator email
name_format: code for name format
returns name of facilitator in specified format
*/
function get_facilitator_name($email, $name_format = NAME_FORMAT_LAST_NAME_FIRST)
{
	global $conn;
	$stmt = $conn->prepare('SELECT first_name, last_name FROM tbl_facilitators WHERE email = :email');
	$stmt->bindValue(':email', $email);
	return execute_fetch_name($stmt, $name_format);
}

/*
get_facilitator:
email: facilitator email
returns specified facilitator in associative array
*/
function get_facilitator($email)
{
	global $conn;
	$stmt = $conn->prepare('SELECT first_name, last_name, is_active
			FROM tbl_facilitators WHERE email = :email');
	$stmt->bindValue(':email', $email);
	return execute_fetch_one($stmt);
}

/*
search_facilitators:
search: term to search for
max_results: maximum results to return
searches for active facilitator by name
*/
function search_facilitators($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email, first_name, last_name
			FROM tbl_facilitators WHERE '.SQL_NAME_SEARCH.
			' AND is_active ORDER BY last_name, first_name, email LIMIT :max_results');
	$stmt->bindValue(':search', $search.'%'); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_inactive_facilitators:
search: term to search for
max_results: maximum results to return
searches for inactive facilitator by name
*/
function search_inactive_facilitators($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email, first_name, last_name
			FROM tbl_facilitators WHERE '.SQL_NAME_SEARCH.
			' AND NOT is_active ORDER BY last_name, first_name, email LIMIT :max_results');
	$stmt->bindValue(':search', $search.'%'); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
facilitator_exists:
email: facilitator email
returns true if facilitator exists in database
*/
function facilitator_exists($email)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email FROM tbl_facilitators WHERE email = :email');
	$stmt->bindValue(':email', $email);
	return execute_exists($stmt);
}

/*
add_facilitator:
email: facilitator email
first_name: facilitator's first name
last_name: facilitator's last name
adds new facilitator to database
returns true if successful
*/
function add_facilitator($email, $first_name, $last_name)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_facilitators (email, first_name, last_name)
			VALUES (:email, :first_name, :last_name)');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':first_name', $first_name);
	$stmt->bindValue(':last_name', $last_name);
	return execute_no_data($stmt);
}

/*
update_facilitator:
email: facilitator email (cannot be changed)
first_name: facilitator's new first name
last_name: facilitator's new last name
is_active: is record active
updates facilitator record
returns true if successful
*/
function update_facilitator($email, $first_name, $last_name, $is_active)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_facilitators
		SET first_name = :first_name, last_name = :last_name, is_active = :is_active
		WHERE email = :email');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':first_name', $first_name);
	$stmt->bindValue(':last_name', $last_name);
	$stmt->bindValue(':is_active', $is_active);
	return execute_no_data($stmt);
}
