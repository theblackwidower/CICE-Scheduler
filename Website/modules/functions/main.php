<?php
/*
redirect:
destination: url to redirect to
Redirect to different page.
*/
function redirect($destination)
{
	ob_clean();
	header("Location: ".$destination);
	//close php document
	exit;
}

/*
is_logged_in:
Returns true if user is logged in.
*/
function is_logged_in()
{
	return isset($_SESSION['login']);
}

/*
get_logged_in_email:
Return email of logged in user.
*/
function get_logged_in_email()
{
	if (is_logged_in())
		return $_SESSION['login']['email'];
	else
		return '';
}

/*
get_logged_in_role:
Return role of logged in user.
*/
function get_logged_in_role()
{
	if (is_logged_in())
		return $_SESSION['login']['role_id'];
	else
		return PUBLIC_ACCESS;
}

/*
default_password:
first_name: user's first name
last_name: user's last name
Return new password based on user's name.
*/
function default_password($first_name, $last_name)
{
	//remove accents
	setlocale(LC_CTYPE, 'en_CA');
	$output = iconv('UTF-8', 'US-ASCII//TRANSLIT', $last_name);
	//last name and first initial
	$output .= iconv('UTF-8', 'US-ASCII//TRANSLIT', $first_name)[0];
	//remove spaces, hyphens, underscores and quotes
	$output = str_replace(array(' ', '-', '_', '"', "'"), '', $output);
	//output lowercase
	return strtolower($output);
}

/*
random_password:
Returns randomly generated password of four random words
*/
function random_password()
{
	require_once "wordlist.php";
	$output = '';
	for ($i = 0; $i < 4; $i++)
		$output .= $wordlist[mt_rand(0, sizeof($wordlist) - 1)];
	return strtolower($output);
}

/*
set_session_message:
message: Message to display.
Sets a message to display on next page load. Typically used before a redirect.
*/
function set_session_message($message)
{
	if (isset($_SESSION['message']))
		$_SESSION['message'] .= '<br />'.$message;
	else
		$_SESSION['message'] = $message;
}

/*
print_session_message:
Outputs session message and resets variable
*/
function print_session_message()
{
	if (isset($_SESSION['message']))
	{
		echo $_SESSION['message'];
		unset($_SESSION['message']);
	}
}

/*
check_default_semester:
Check if default semester is valid. If it's not set, sets it to current or next semester.
*/
function check_default_semester()
{
	if (!isset($_SESSION['semester']))
		$_SESSION['semester'] = get_current_semester();

	if ($_SESSION['semester'] === false)
	{
		set_session_message('The current semester has not been registered.');
		redirect('administration.php');
	}
}

/*
get_default_semester:
Returns the id of the default semester.
*/
function get_default_semester()
{
	check_default_semester();
	return $_SESSION['semester'];
}

/*
set_default_semester:
semester_id: id of semester to set.
Records what we want to use as the default semester.
*/
function set_default_semester($semester_id)
{
	if (is_registered_semester($semester_id))
	{
		$_SESSION['semester'] = $semester_id;
		return true;
	}
	else
		return false;
}


/*
parse_room_number:
room_number: room number to parse
Formats room number for storage in the database.
*/
function parse_room_number($room_number)
{
	$return = "";

	for ($i = 0; $i < strlen($room_number); $i++)
	{
		if (ctype_alpha($room_number[$i]))
			$return .= strtoupper($room_number[$i]);
		else if (ctype_digit($room_number[$i]))
			$return .= $room_number[$i];
	}

	return $return;
}


/*
parse_course_code:
course_code: course code to parse
Formats course code for storage in the database.
*/
function parse_course_code($course_code)
{
	$course_code_1 = "";
	$course_code_2 = "";

	$is_second_half = false;

	for ($i = 0; $i < strlen($course_code); $i++)
	{
		if (ctype_alpha($course_code[$i]))
		{
			if (!$is_second_half)
				$course_code_1 .= strtoupper($course_code[$i]);
			else
				$course_code_2 .= strtoupper($course_code[$i]);
		}
		else if (ctype_digit($course_code[$i]))
		{
			$is_second_half = true;
			$course_code_2 .= $course_code[$i];
		}
	}

	if (strlen($course_code_1) <= 5 && strlen($course_code_2) <= 5 &&
		strlen($course_code_1) > 0 && strlen($course_code_2) > 0)
	{
		return str_pad(strtoupper($course_code_1), 5).$course_code_2;
	}
	else
		return false;
}

/*
format_name:
first_name: First name of subject
last_name: Last name of subject
format_code: Code identifying format of name
Formats a name for displaying.
*/
function format_name($first_name, $last_name, $format_code)
{
	switch ($format_code)
	{
		case NAME_FORMAT_LAST_NAME_FIRST:
			return $last_name.', '.$first_name;
		case NAME_FORMAT_FIRST_NAME_FIRST:
			return $first_name.' '.$last_name;
		case NAME_FORMAT_FIRST_INITIAL_LAST_NAME:
			return $first_name[0].'. '.$last_name;
		case NAME_FORMAT_LAST_NAME_FIRST_INITIAL:
			return $last_name.' '.$first_name[0].'.';
		case NAME_FORMAT_FIRST_NAME_LAST_INITIAL:
			return $first_name.' '.$last_name[0].'.';
	}
}

/*
format_time:
hour: Hour of time on a 24 hour clock.
minute: Minute of time. (optional)
Formats a time for displaying.
*/
function format_time($hour, $minute = 0)
{
	$output = '';
	if ($hour == 0)
		$output .= '12';
	else if ($hour <= 12)
		$output .= $hour;
	else
		$output .= ($hour - 12);

	$output .= ':'.str_pad($minute, 2, '0', STR_PAD_LEFT);

	if ($hour < 12)
		$output .= ' am';
	else
		$output .= ' pm';

	return $output;
}

/*
ends_with:
haystack: _tring to search needle for.
needle: String to find in haystack
Returns true if haystack ends with needle.
*/
function ends_with($haystack, $needle)
{
	return (strpos($haystack, $needle) === (strlen($haystack) - strlen($needle)));
}
