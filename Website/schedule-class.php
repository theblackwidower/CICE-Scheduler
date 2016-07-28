<?php
include_once "modules/constants.php";
$title = "Schedule Facilitators";
$restrictionCode = ROLE_ADMIN;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$semester_id = $_POST['semester_id'];
	$course_rn = $_POST['course_rn'];
	$room_number = $_POST['room_number'];
	$day_id = $_POST['day_id'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];

	$operation = $_POST['operation'];
	$selected_id = $_POST['selected_id'];


	if ($operation == 'add')
		schedule_facilitator($semester_id, $course_rn, $day_id, $start_time, $selected_id);
	else if ($operation == 'remove')
		unschedule_facilitator($semester_id, $course_rn, $day_id, $start_time, $selected_id);
	else if ($operation == 'quick-students')
	{
		$to_add = MAX_STUDENTS_PER_FACILITATOR -
				count_assigned_students($semester_id, $course_rn, $day_id, $start_time, $selected_id);
		$unassigned_students = unassigned_students_by_class($semester_id, $course_rn, $day_id, $start_time);
		while ((list($i, $student) = each($unassigned_students)) && $i < $to_add)
			assign_student($semester_id, $course_rn, $day_id, $start_time, $selected_id, $student['student_id']);
	}
}
else
{
	if (isset($_GET['crn']) && isset($_GET['day']) && isset($_GET['time']))
	{
		$semester_id = get_default_semester();
		$course_rn = $_GET['crn'];
		$day_id = $_GET['day'];
		$start_time = $_GET['time'];
		$data = get_class_time_by_crn($course_rn, $day_id, $start_time, $semester_id);
		if ($data !== false)
		{
			$end_time = $data['end_time'];
			$room_number = $data['room_number'];
		}
		else
		{
			set_session_message("Class is not currently registered in the system.");
			redirect('schedule-build.php');
		}
	}
	else
	{
		set_session_message("Please select a class.");
		redirect('schedule-build.php');
	}
}
?>
	<form id="class_data" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?'.http_build_query($_GET);?>">
		<ul>
			<?php
			form_read_only('semester_id', 'Semester', $semester_id);
			form_read_only('course_rn', 'CRN', $course_rn);
			form_read_only('room_number', 'Room', $room_number);
			form_read_only('day_id', 'Day', $day_id);
			form_read_only_time($start_time, $end_time);
			?>
		</ul>
		<ul>
			<?php
			$student_count = count_students_in_class($semester_id, $course_rn);
			form_read_only('student_count', 'Number Of Students', $student_count);
			form_read_only('facilitator_requirement', 'Number Of Required Facilitators', ceil($student_count / MAX_STUDENTS_PER_FACILITATOR));
			?>
		</ul>
		<ul>
			<?php
			$unassigned_students = unassigned_students_by_class($semester_id, $course_rn, $day_id, $start_time);
			if (count($unassigned_students) > 0)
				form_read_only_list('students', 'Unassigned Students', $unassigned_students, 'student_id');
			?>
			<input id="selected_id" name="selected_id" type="hidden" value="" />
			<input id="operation" name="operation" type="hidden" value="" />
		</ul>
	</form>
	<?php
	popup_casing('schedule');
	$booked_facilitators = get_booked_facilitators($semester_id, $day_id, $start_time, $course_rn);
	if (count($booked_facilitators) > 0)
	{
		$extra_info = array('semester_id' => $semester_id, 'course_rn' => $course_rn, 'day_id' => $day_id, 'start_time' => $start_time);
		echo '<div class="search_results">';
			display_search_results('booked-facilitators-scheduling', $booked_facilitators, $extra_info);
		echo '</div>';
	}
	echo '<h2>Available Facilitators</h2>';
	$available_facilitators = search_available_facilitators($semester_id, $day_id, $start_time, $end_time, get_rooms_campus($room_number));
	echo '<div class="search_results">';
		display_search_results('available-facilitators-scheduling', $available_facilitators, $_GET);
	echo '</div>';

include "modules/footer.php";
