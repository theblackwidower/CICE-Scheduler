<?php
require_once "../modules/constants.php";
$restrictionCode = ROLE_DATA_ENTRY;
require_once '../modules/init.php';

$email = $_GET['email'];
$semester_id = get_default_semester();

if (facilitator_exists($email))
{
	$schedule = get_facilitator_schedule($email, $semester_id);
	if (isset($_GET['crn']) && isset($_GET['day']) && isset($_GET['time']))
	{
		$new_class['day_id'] = $_GET['day'];
		$new_class['start_time'] = $_GET['time'];
		$new_class['course_rn'] = $_GET['crn'];
		$new_class['class_role'] = SCHEDULE_ROLE_NEW;

		$time_data = get_class_time_by_crn($new_class['course_rn'], $new_class['day_id'],
				$new_class['start_time'], $semester_id);
		$class_data = get_class_rn($new_class['course_rn'], $semester_id);
		
		if ($time_data !== false && $class_data !== false)
			$schedule[] = array_merge($new_class, $time_data, $class_data);
	}
	echo build_timetable(START_SCHEDULE, END_SCHEDULE, $schedule, TT_LINK_NONE);
}
else
	echo "<h2><em>".$email."</em> is not registered as a facilitator.</h2>";
