<?php
/*
login:
email: user's email address.
password: password provided by user attempting to login
for logging into application. Returns true on sucessful login.
*/
function login($email, $password)
{
	global $conn;
	$stmt = $conn->prepare('SELECT password, role_id FROM tbl_users WHERE :email = email');
	$stmt->bindValue(':email', $email);
	$data = execute_fetch_obj($stmt);
	if ($data === false || !password_verify($password, $data->password))
		return false;
	else if ($data->role_id == ROLE_DISABLED)
	{
		set_session_message("Your account has been disabled.");
		return true;
	}
	else
	{
		setcookie("email", $email, time() + COOKIE_EXPIRY);
		$_SESSION['login'] = Array('email' => $email, 'role_id' => $data->role_id);
		return true;
	}
}

/*
set_password:
email: user's email address.
password: user's new password
for changing password of login account. Returns true if successful.
*/
function set_password($email, $password)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_users SET password = :password WHERE email = :email');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
	return execute_no_data($stmt);
}

/*
user_exists:
email: user email
returns true if user exists in database
*/
function user_exists($email)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email FROM tbl_users WHERE email = :email');
	$stmt->bindValue(':email', $email);
	return execute_exists($stmt);
}

/*
change_email:
old_email: email address the user is currently registered under
new_email: email address the user is now using.
change user's email address. If account is linked to a facilitator account,
	the change will affect those records as well.
returns true if successful
*/
function change_email($old_email, $new_email)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_users
		SET email = :new_email WHERE email = :old_email');
	$stmt->bindValue(':old_email', $old_email);
	$stmt->bindValue(':new_email', $new_email);
	return execute_no_data($stmt);
}

/*
change_user_role:
email: user's email address
role_id: the role the user is now assigned to.
change user's role.
returns true if successful
*/
function change_user_role($email, $role_id)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_users
		SET role_id = :role WHERE email = :email');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':role', $role_id);
	return execute_no_data($stmt);
}

/*
get_all_users:
max_results: maximum results to return
returns all users in associative array
*/
function get_all_users($max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email, role_id FROM tbl_users ORDER BY email LIMIT :max_results');
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
search_users:
search: term to search for
max_results: maximum results to return
searches for user by email address
*/
function search_users($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email, role_id
			FROM tbl_users WHERE LOWER(email) LIKE LOWER(:search) ORDER BY email LIMIT :max_results');
	$stmt->bindValue(':search', '%'.$search.'%'); //'%' is wildcard in PostgreSQL
	$stmt->bindValue(':max_results', $max_results);
	return execute_fetch_all($stmt);
}

/*
get_user_role:
email: user email
returns specified user's role
*/
function get_user_role($email)
{
	global $conn;
	$stmt = $conn->prepare('SELECT role_id FROM tbl_users WHERE email = :email');
	$stmt->bindValue(':email', $email);
	return execute_fetch_param($stmt, 'role_id');
}

/*
add_user_account:
email: user's email
password: user's password
role_id: user's role
adds new user to database
returns true if successful
*/
function add_user_account($email, $password, $role_id)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_users (email, password, role_id)
			VALUES (:email, :password, :role)');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
	$stmt->bindValue(':role', $role_id);
	return execute_no_data($stmt);
}

/*
get_role_name:
role_id: role id
returns name of role
*/
function get_role_name($role_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT description FROM tbl_role WHERE role_id = :role');
	$stmt->bindValue(':role', $role_id);
	return execute_fetch_param($stmt, 'description');
}

/*
get_all_roles:
returns all user roles in associative array
*/
function get_all_roles()
{
	global $conn;
	$stmt = $conn->prepare('SELECT role_id, description FROM tbl_role');
	return execute_fetch_all($stmt);
}
