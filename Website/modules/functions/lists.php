<?php
/*
display_search_results:
id: type of results to display
results: results of search
extra_info: additional info specific to search
	used for the scheduling of facilitators, and displaying timetable info, holds course info
Displays the results of a search, either AJAX or on page load. Includes pagination.
*/
function display_search_results($id, $results, $extra_info = null)
{
	echo '<div class="search_results">';
	if (sizeof($results) > 0)
	{
		$page_count = ceil(count($results) / MAX_RESULTS_PER_PAGE);

		echo '<span class="active_page_store hidden">1</span>';

		$page_nav = '';
		if ($page_count > 1)
		{
			$page_nav .= '<div class="page_nav">';

			$page_nav .= '<a href="#" onclick="search_page_shift(-1, this.parentNode.parentNode); return false;" class="back_arrow invalid">&#8592;</a> ';
			for ($i = 1; $i <= $page_count; $i++)
			{
				$page_nav .= '<a href="#" onclick="search_page('.$i.', this.parentNode.parentNode); return false;" class="number';
				if ($i == 1)
					$page_nav .= ' selected_page';
				$page_nav .= '">'.$i.'</a> ';
			}
			$page_nav .= '<a href="#" onclick="search_page_shift(1, this.parentNode.parentNode); return false;" class="forward_arrow">&#8594;</a>';

			$page_nav .= '<a href="#" onclick="search_page_all(this.parentNode.parentNode); return false;" class="view_all">View All</a>';

			$page_nav .= '</div>';
		}

		echo $page_nav;

		echo '<ul class="block_list page_1">';
		$i = 0;
		while ($i < count($results))
		{
			if ($id == 'users')
				echo display_user_li($results[$i]);
			else if ($id == 'facilitators')
				echo display_facilitator_li($results[$i]);
			else if ($id == 'professors')
				echo display_professor_li($results[$i]);
			else if ($id == 'students')
				echo display_student_li($results[$i]);
			else if ($id == 'courses')
				echo display_course_li($results[$i]);
			else if ($id == 'classes')
				echo display_class_li($results[$i]);
			else if ($id == 'scheduling')
				echo display_class_for_scheduling_li($results[$i], false);
			else if ($id == 'new-scheduling')
				echo display_class_for_scheduling_li($results[$i], true);
			else if ($id == 'booked-facilitators-scheduling')
				echo display_booked_facilitators_li($results[$i]['facilitator'], $extra_info['semester_id'],
					$extra_info['course_rn'], $extra_info['day_id'], $extra_info['start_time']);
			else if ($id == 'available-facilitators-scheduling')
				echo display_available_facilitators_li($results[$i]['email'], $extra_info);
			else if ($id == 'assigned-students-scheduling')
				echo display_booked_students_li($results[$i]['student_id']);
			else if ($id == 'unassigned-students-scheduling')
				echo display_available_students_li($results[$i]['student_id']);

			$i++;

			if ($i % MAX_RESULTS_PER_PAGE == 0 && $i < count($results))
				echo '</ul><ul class="block_list hidden page_'.(($i / MAX_RESULTS_PER_PAGE) + 1).'">';
		}
		if ($page_count > 1)
		{
			while ($i % MAX_RESULTS_PER_PAGE != 0)
			{
				echo '<li class="spacer"><div></div></li>';
				$i++;
			}
		}

		echo '</ul>';

		echo $page_nav;
	}
	else
		echo '<h2>No Records Found</h2>';

	echo '</div>';
}

/*
display_user_li:
user: All user info in associative array
Display user for user management.
*/
function display_user_li($user)
{
	return '
	<li>
		<div>
			<span class="email">'.$user['email'].'</span>
			<span class="role">'.get_role_name($user['role_id']).'</span>
		</div>
		<a class="password" href="user-password-reset.php?email='.urlencode($user['email']).'">
			Reset<br />Password
		</a>
		<a class="edit" href="user-role.php?email='.urlencode($user['email']).'">
			Change<br />Role
		</a>
	</li>';
}

/*
display_student_li:
student: All student info in associative array
Display student for student record management
*/
function display_student_li($student)
{
	return '
	<li>
		<div>
			<span class="name">'.
			format_name($student['first_name'], $student['last_name'], NAME_FORMAT_LAST_NAME_FIRST).'</span>
			<span class="id">'.$student['student_id'].'</span>
		</div>
		<a class="edit" href="student-edit.php?id='.urlencode($student['student_id']).'">
			Edit<br />Record
		</a>
		<a class="schedule" href="student-schedule.php?id='.urlencode($student['student_id']).'">
			Edit<br />Schedule
		</a>
	</li>';
}

/*
display_facilitator_li:
facilitator: All facilitator info in associative array
Display facilitator for facilitator record management
*/
function display_facilitator_li($facilitator)
{
	return '
	<li>
		<div>
			<span class="name">'.
			format_name($facilitator['first_name'], $facilitator['last_name'], NAME_FORMAT_LAST_NAME_FIRST).'</span>
			<span class="id">'.$facilitator['email'].'</span>
		</div>
		<a class="edit" href="facilitator-edit.php?email='.urlencode($facilitator['email']).'">
			Edit<br />Record
		</a>
		<a class="schedule" href="facilitator-schedule.php?email='.urlencode($facilitator['email']).'">
			View<br />Schedule
		</a>
	</li>';
}

/*
display_course_li:
course: All course info in associative array
Display course for course record management
*/
function display_course_li($course)
{
	return '
	<li>
		<div>
			<span class="course_code">'.$course['course_code'].'</span>
			<span class="course_name">'.$course['course_name'].'</span>
		</div>
		<a class="edit" href="course-edit.php?code='.urlencode($course['course_code']).'">
			Edit<br />Course
		</a>
		<a class="schedule" href="class-list.php?code='.urlencode($course['course_code']).'">
			View<br />Classes
		</a>
	</li>';
}

/*
display_class_li:
class: All class info in associative array
Display class for class record management
*/
function display_class_li($class)
{
	return '
	<li>
		<div>
			<span class="crn">'.$class['course_rn'].'</span>
			<span class="professor_name">'.
			format_name($class['first_name'], $class['last_name'], NAME_FORMAT_LAST_NAME_FIRST).'</span>
		</div>
		<a class="edit" href="class-edit.php?crn='.urlencode($class['course_rn']).'">
			Edit<br />Class
		</a>
		<a class="schedule" href="class-schedule.php?crn='.urlencode($class['course_rn']).'">
			View<br />Schedule
		</a>
	</li>';
}

/*
display_professor_li:
professor: All professor info in associative array
Display professor for professor record management
*/
function display_professor_li($professor)
{
	return '
	<li>
		<div>
			<span class="name">'.format_name($professor['first_name'], $professor['last_name'], NAME_FORMAT_LAST_NAME_FIRST).'</span>
			<span class="email">'.$professor['email'].'</span>
		</div>
		<a class="edit" href="professor-edit.php?id='.urlencode($professor['professor_id']).'">
			Edit<br />Professor
		</a>
	</li>';
}

/*
display_room_li:
room: All room info in associative array
Display room in simple list.
*/
function display_room_li($room)
{
	return '<li>'.$room['room_number'].'</li>';
}

/*
display_registration_ul:
registration_list: All courses a student is registered for in associative array
student_id: id of student registered
List all courses a student is registered for.
*/
function display_registration_ul($registration_list, $student_id)
{
	$output = '<ul id="registered_courses">';
	foreach ($registration_list as $class)
		$output .= '
		<li>
			<span class="crn">'.$class['course_rn'].'</span>
			<span class="course_code">'.$class['course_code'].'</span>
			<span class="course_name">'.$class['course_name'].'</span>
			<a href="student-schedule-delete.php?id='.$student_id.'&amp;crn='.$class['course_rn'].'">Delete</a>
			<span class="professor">'.get_professor_name($class['professor_id'], NAME_FORMAT_LAST_NAME_FIRST_INITIAL).'</span>
		</li>';

	$output .= '</ul>';
	return $output;
}

/*
display_autocomplete_item:
item: All info on item in associative array
value_id: field name of the item value
title_id: field name of the item's display name
subtitle_id: field name of the item's second display name
Output the results of each item in a search for form autocomplete.
*/
function display_autocomplete_item($item, $value_id, $title_id, $subtitle_id)
{
	$value = $item[$value_id];
	if ($title_id == 'name')
		$title = format_name($item['first_name'], $item['last_name'], NAME_FORMAT_LAST_NAME_FIRST);
	else
		$title = $item[$title_id];
	$subtitle = $item[$subtitle_id];
	echo '
		<a href="#" onclick="complete(this, \''.$value.'\'); return false;">
			<span class="title">'.$title.'</span>
			<span class="subtitle">'.$subtitle.'</span>
		</a>';
}

/*
display_class_for_scheduling_li:
block: All info for class block
is_new: Boolean, should the Quick Schedule button be displayed?
Display class info for scheduling facilitators.
*/
function display_class_for_scheduling_li($block, $is_new)
{
	$course = get_course_from_crn($block['course_rn'], get_default_semester());
	$return = '
	<li>
		<div>
			<span class="crn">'.$block['course_rn'].'</span>
			<span class="time">
				'.get_day_name($block['day_id']).' at '.format_time($block['start_time']).'
			</span>
			<span class="course">'.$course['course_code'].' - '.$course['course_name'].'</span>
		</div>
		<a class="edit" href="schedule-class.php?crn='.urlencode($block['course_rn']).
				'&amp;day='.urlencode($block['day_id']).'&amp;time='.urlencode($block['start_time']).'">
			Schedule<br />Facilitators
		</a>';
	if ($is_new)
		$return .= '
		<a href="#" class="add" onclick="quick_schedule(\''.$block['course_rn'].'\',
				\''.$block['day_id'].'\', \''.$block['start_time'].'\'); return false;">
			Quick<br />Schedule
		</a>';
	$return .= '</li>';
	return $return;
}

/*
display_available_facilitators_li:
email: facilitator's email address
class_info: all class info in associative array
Display facilitator for assigning to class.
*/
function display_available_facilitators_li($email, $class_info)
{
	return '
	<li>
		<div>
			<span class="name">'.get_facilitator_name($email, NAME_FORMAT_LAST_NAME_FIRST).'</span>
			<span class="id">'.$email.'</span>
		</div>
		<a href="#" class="schedule"
				onclick="new_popup(\'schedule\', \'schedule.php?email='.
				urlencode($email).'&amp;'.http_build_query($class_info).'\'); return false;">
			View<br />Schedule
		</a>
		<a href="#" class="add" onclick="quick_post_submit(\''.$email.'\', \'add\'); return false;">
			Add<br />Facilitator
		</a>
	</li>';
}

/*
display_booked_facilitators_li:
email: facilitator's email address
semester_id: Id for current semester
course_rn: crn of relevant class
day_id: day class occurs on
start_time: time class starts
Display facilitator already assigned to class.
*/
function display_booked_facilitators_li($email, $semester_id, $course_rn, $day_id, $start_time)
{
	$result = '
	<li>
		<div class="booked_facilitator">
			<span class="name">'.get_facilitator_name($email, NAME_FORMAT_LAST_NAME_FIRST).'</span>
			<span class="id">'.$email.'</span>
		</div>
		<a class="edit" href="schedule-students.php?crn='.urlencode($course_rn).'&amp;day='.urlencode($day_id).
				'&amp;time='.urlencode($start_time).'&amp;facilitator='.urlencode($email).'">
			Assign<br />Students
		</a>
		<a href="#" class="quick" onclick="quick_post_submit(\''.$email.'\', \'quick-students\'); return false;">
			Quick<br />Assign
		</a>
		<a class="schedule" href="#"
				onclick="new_popup(\'schedule\', \'schedule.php?email='.
				urlencode($email).'\'); return false;">
			View<br />Schedule
		</a>
		<a href="#" class="add" onclick="quick_post_submit(\''.$email.'\', \'remove\'); return false;">
			Remove<br />Facilitator
		</a>
		<div class="student_group">';
	$students = get_assigned_students($semester_id, $course_rn, $day_id, $start_time, $email);
	foreach ($students as $student)
		$result .= '<span>'.get_student_name($student['student_id'], NAME_FORMAT_LAST_NAME_FIRST).'</span>';
	$result .= '</div></li>';
	return $result;
}

/*
display_available_students_li:
student_id: Id of student
Display student for assigning to facilitator
*/
function display_available_students_li($student_id)
{
	return '
	<li>
		<div>
			<span class="name">'.get_student_name($student_id, NAME_FORMAT_LAST_NAME_FIRST).'</span>
			<span class="id">'.$student_id.'</span>
		</div>
		<a href="#" class="add" onclick="quick_post_submit(\''.$student_id.'\', \'add\'); return false;">
			Assign<br />Student
		</a>
	</li>';
}

/*
display_booked_students_li:
student_id: Id of student
Display student already assigned to facilitator
*/
function display_booked_students_li($student_id)
{
	return '
	<li>
		<div>
			<span class="name">'.get_student_name($student_id, NAME_FORMAT_LAST_NAME_FIRST).'</span>
			<span class="id">'.$student_id.'</span>
		</div>
		<a href="#" class="add" onclick="quick_post_submit(\''.$student_id.'\', \'remove\'); return false;">
			Unassign<br />Student
		</a>
	</li>';
}
