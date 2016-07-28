<?php
include_once "modules/constants.php";
$title = "Build Schedule";
$restrictionCode = ROLE_ADMIN;
include "modules/header.php";
$semester_id = get_default_semester();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$course_rn = $_POST['course_rn'];
	$day_id = $_POST['day_id'];
	$start_time = $_POST['start_time'];

	$data = get_class_time_by_crn($course_rn, $day_id, $start_time, $semester_id);
	if ($data !== false)
	{
		$end_time = $data['end_time'];
		$room_number = $data['room_number'];
		$processed = array();

		$to_add = ceil(count_students_in_class($semester_id, $course_rn) / MAX_STUDENTS_PER_FACILITATOR) -
				count_assigned_facilitators($semester_id, $course_rn, $day_id, $start_time);

		$all_facilitators = search_available_facilitators($semester_id, $day_id, $start_time, $end_time, get_rooms_campus($room_number));
		while ((list($i, $facilitator) = each($all_facilitators)) && $i < $to_add)
		{
			schedule_facilitator($semester_id, $course_rn, $day_id, $start_time, $facilitator['email']);
			$processed[] = get_facilitator_name($facilitator['email'], NAME_FORMAT_FIRST_NAME_FIRST);
		}

		$is_student_assigned = false;

		$all_facilitators = get_booked_facilitators($semester_id, $day_id, $start_time, $course_rn);
		foreach ($all_facilitators as $facilitator)
		{
			$to_add = MAX_STUDENTS_PER_FACILITATOR -
					count_assigned_students($semester_id, $course_rn, $day_id, $start_time, $facilitator['facilitator']);
			$unassigned_students = unassigned_students_by_class($semester_id, $course_rn, $day_id, $start_time);
			while ((list($i, $student) = each($unassigned_students)) && $i < $to_add)
			{
				assign_student($semester_id, $course_rn, $day_id, $start_time, $facilitator['facilitator'], $student['student_id']);
				$is_student_assigned = true;
			}
		}
		if (count($processed) < 1)
			$message = 'No facilitators were';
		else if (count($processed) == 1)
			$message = $processed[0].' has been';
		else
		{
			$message = '';
			for ($i = 0; $i < count($processed) - 2; $i++)
				$message .= $processed[$i].', ';
			$message .= $processed[count($processed) - 2].' and '.$processed[count($processed) - 1];
			$message .= ' have been';
		}
		echo '<h3>'.$message.' booked for '.$course_rn.' at '.get_day_name($day_id).', '.
				format_time($start_time).'.<br />';
		if ($is_student_assigned)
			echo 'Students have been assigned.<br />';
		echo '<a href="schedule-class.php?crn='.urlencode($course_rn).
				'&day='.urlencode($day_id).'&time='.urlencode($start_time).'">Details</a></h3>';
	}
}

$unpaired_students = search_for_classes_with_unpaired_students($semester_id);
$overbooked_classes = search_for_overbooked_classes($semester_id);
if (count($unpaired_students) + count($overbooked_classes) > 0)
{
	if (count($unpaired_students) > 0)
	{
		echo '<h2>Classes with Unpaired Students</h2>';
		echo '<div class="search_results">';
			display_search_results('new-scheduling', $unpaired_students);
		echo '</div>';
		echo '
		<form id="quick_add" method="post" action="'.$_SERVER['PHP_SELF'].'">
		<input id="course_rn" name="course_rn" type="hidden" value="" />
		<input id="day_id" name="day_id" type="hidden" value="" />
		<input id="start_time" name="start_time" type="hidden" value="" />
		</form>';
	}
	if (count($overbooked_classes) > 0)
	{
		echo '<h2>Overbooked Classes</h2>';
		echo '<div class="search_results">';
			display_search_results('scheduling', $overbooked_classes);
		echo '</div>';
	}
}
else
{
	echo '<h2>All students have been paired.<br />No class is overbooked.</h2>';
}
include "modules/footer.php";
