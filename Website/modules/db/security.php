<?php
/*
login:
email: user's email address.
password: password provided by user attempting to login
for logging into application.
Returns 0 on failed login.
Returns 1 on sucessful login or disabled account.
Returns 2 on sucessful login and password change required.
*/
function login($email, $password)
{
	global $conn;
	$stmt = $conn->prepare('SELECT password, role_id, force_new_password FROM tbl_users WHERE :email = email');
	$stmt->bindValue(':email', $email);
	$data = execute_fetch_obj($stmt);
	if ($data === false)
	{
		password_hash($password, PASSWORD_DEFAULT); //prevent timing attack
		return 0;
	}
	else if (!password_verify($password, $data->password))
		return 0;
	else if ($data->role_id == ROLE_DISABLED)
	{
		set_session_message("Your account has been disabled.");
		return 1;
	}
	else
	{
		setcookie("email", $email, time() + COOKIE_EXPIRY);
		$_SESSION['login'] = Array('email' => $email, 'role_id' => $data->role_id);
		$_SESSION['password_change'] = $data->force_new_password;

		if ($data->force_new_password)
			return 2;
		else
			return 1;
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
set_password_change_force:
email: user's email address.
requirement: whether user needs to change their password at next login.
for forcing user to change password of login account, or record that password was changed.
Returns true if successful.
*/
function set_password_change_force($email, $requirement)
{
	global $conn;
	$stmt = $conn->prepare('UPDATE tbl_users SET force_new_password = :requirement WHERE email = :email');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':requirement', $requirement);
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
searches for user by email address or role description
*/
function search_users($search, $max_results = MAX_SEARCH_RESULT)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email, tbl_users.role_id
			FROM tbl_users, tbl_role WHERE tbl_users.role_id = tbl_role.role_id
			AND (email ILIKE :search OR description ILIKE :search) ORDER BY email LIMIT :max_results');
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
function add_user_account($email, $password, $role_id, $force_new_password = 'true')
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_users (email, password, role_id, force_new_password)
			VALUES (:email, :password, :role, :force_new_password)');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
	$stmt->bindValue(':role', $role_id);
	$stmt->bindValue(':force_new_password', $force_new_password);
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
