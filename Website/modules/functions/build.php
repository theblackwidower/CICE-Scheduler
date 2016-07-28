<?php
/*
build_sidebar:
sidebar_data: contents of sidebar menu
	submit data this way
	array($name => $address, ...);
	if item is a category
	array($name => $address, $name => array($name => $address, ...), ...);
source: the address of the current page
To build the contents of the sidebar for display
*/
function build_sidebar($sidebar_data, $source)
{
	$result = '';
	$has_current = false;
	foreach ($sidebar_data as $name => $address)
	{
		if (!is_string($name))
			$name = '';
		if (is_array($address))
		{
			$content = build_sidebar($address, $source);

			$result .= '<span onclick="sidebar_click(event);"';

			if ($content['current'] === true)
			{
				$result .= ' class="open"';
				$has_current = true;
			}
			$result .= '>'.$name.$content['output'].'</span>';
		}
		else if ($source == $address)
		{
			$result .= '<span class="current">'.$name.'</span>';
			$has_current = true;
		}
		else
			$result .= '<a href="'.SITE_FOLDER.$address.'">'.$name.'</a>';
	}
	return array('output' => $result, 'current' => $has_current);
}

/*
build_timetable:
start_schedule: Time (24h) to start schedule
end_schedule: Time (24h) to end schedule
contents: schedule data in associative array
include_link: code for which link to include in timetable cells
To build a timetable for display
*/
function build_timetable($start_schedule, $end_schedule, $contents, $include_link = TT_LINK_NONE)
{
	$table = array();
	$days = get_all_days();
	for ($i = $start_schedule; $i < $end_schedule; $i++)
	{
		$table[$i] = array();
		foreach ($days as $day)
			$table[$i][$day['day_id']] = '>';
	}
	foreach ($contents as $class)
	{
		$start_time = $class['start_time'];
		$end_time = $class['end_time'];
		$day_id = $class['day_id'];
		if (isset($table[$start_time][$day_id]) && $table[$start_time][$day_id] == '>')
		{
			$class_data = format_timetable_cell($class, $include_link);

			if ($start_time + 1 != $end_time)
			{
				$table[$start_time][$day_id] = ' rowspan="'.($end_time - $start_time).'">';
				for ($i = $start_time + 1; $i < $end_time; $i++)
				{
					if (isset($table[$i][$day_id]) && $table[$i][$day_id] == '>')
						unset($table[$i][$day_id]);
					else
						return schedule_error($contents, $day_id, $i, $include_link);	//we have an error
				}
			}

			if (isset($class['class_role']) && $class['class_role'] == SCHEDULE_ROLE_NEW)
				$table[$start_time][$day_id] = substr($table[$start_time][$day_id], 0, -1).' class="new_item">';

			$table[$start_time][$day_id] .= $class_data;
		}
		else
			return schedule_error($contents, $day_id, $start_time, $include_link);	//we have an error
	}
	$output = '<table class="schedule"><tr><th></th>';
	foreach ($days as $day)
		$output .= '<th>'.$day['day_name'].'</th>';
	$output .= '</tr>';
	foreach ($table as $time => $row)
	{
		$output .= '<tr><td>'.format_time($time).'<br /><br /><br /><br /><br /><br /></td>';
		foreach ($row as $day => $data)
			$output .= '<td'.$data.'</td>';
		$output .= '</tr>';
	}
	$output .= '<tr><td>'.format_time($end_schedule).'</td></tr>';
	$output .= '</table>';
	return $output;
}

/*
build_timetable:
class: class data in associative array
include_link: code for which link to include in timetable cells
To build a the contents of a timetable cell for display
*/
function format_timetable_cell($class, $include_link)
{
	$output = '';
	if (isset($class['course_code']))
		$output .= '<span class="course_code">'.$class['course_code'].'</span>';
	if (isset($class['course_rn']))
		$output .= '<span class="course_rn">'.$class['course_rn'].'</span>';
	if (isset($class['room_number']))
	{
		$output .= '<span class="campus_name">'.get_campus_name_from_room($class['room_number']).'</span>';
		$output .= '<span class="room_number">'.$class['room_number'].'</span>';
	}
	if (isset($class['professor_id']))
		$output .= '<span class="professor_name">'.get_professor_name($class['professor_id'], NAME_FORMAT_FIRST_INITIAL_LAST_NAME).'</span>';

	if (isset($class['students']) && strlen($class['students']) > 2)
	{
		$output .= '<span class="students">';
		$student_ids = explode(',', substr($class['students'], 1, -1));
		foreach ($student_ids as $student_id)
			$output .= get_student_name($student_id, NAME_FORMAT_FIRST_NAME_LAST_INITIAL).'<br />';
		$output .= '</span>';
	}

	if ($include_link == TT_LINK_CLASS_TIME_DELETE)
		$output .= '<a href="class-time-delete.php?day='.urlencode($class['day_id']).
			'&time='.urlencode($class['start_time']).'&room='.urlencode($class['room_number']).'">Delete</a>';
	else if ($include_link == TT_LINK_SCHEDULE_EDIT && $class['class_role'] == SCHEDULE_ROLE_FACILITATE)
		$output .= '<a href="schedule-class.php?crn='.urlencode($class['course_rn']).
			'&day='.urlencode($class['day_id']).'&time='.urlencode($class['start_time']).'">Edit</a>';
	else if ($include_link == TT_LINK_SCHEDULE_EDIT_FROM_STUDENT)
		$output .= '<a href="schedule-class.php?crn='.urlencode($class['course_rn']).
			'&day='.urlencode($class['day_id']).'&time='.urlencode($class['start_time']).'">Schedule<br />Facilitators</a>';

	return $output;
}

/*
schedule_error:
contents: schedule data in associative array
error_day: day when error was first encountered
error_time: time when error was first encountered
include_link: code for which link to include in timetable cells
Handles errors in the construction of a timetable
*/
function schedule_error($contents, $error_day, $error_time, $include_link)
{
	$output = 'Conflict exists in schedule.<br />Found on '.
			get_day_name($error_day).' @ '.format_time($error_time).
			'<ul class="timetable_error">';

	foreach ($contents as $class)
	{
		if ($error_day == $class['day_id'])
		{
			$output .= '<li>';
			$output .= '<span class="time">'.format_time($class['start_time']).' - '.format_time($class['end_time']).'</span>';
			$output .= format_timetable_cell($class, $include_link);
			$output .= '</li>';
		}
	}
	$output .= '</ul>';
	return $output;
}

/*
email_password:
email: email address to send message to
password: password to send over email
Sends password to user through email.
*/
function email_password($email, $password)
{
	$message = "Hello ".get_facilitator_name($email, NAME_FORMAT_FIRST_NAME_FIRST)."\r\n";
	$message .= "You are recieving this message because an account has been created for you on the CICE Scheduler.\r\n";
	$message .= "On this site, you can access your latest schedule as it's updated.\r\n";
	$message .= "Your login email is: ".$email."\r\n";
	$message .= "Your password is: ".$password."\r\n";
	$message .= "Please note, this password is case-sensitive. We suggest you immediately change your password to something more memorable as soon as possible.\r\n";
	$message .= "Sincerely, Me.";

	return mail($email_address, "Your New Account on the CICE Scheduler", $message, "From: no-reply@durhamcollege.ca\r\n" );
}
