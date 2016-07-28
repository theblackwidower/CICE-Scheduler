<?php
include_once "modules/constants.php";
$title = "Assign Students";
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
	$facilitator = $_POST['facilitator'];

	$operation = $_POST['operation'];
	$selected_id = $_POST['selected_id'];

	if ($operation == 'add')
		assign_student($semester_id, $course_rn, $day_id, $start_time, $facilitator, $selected_id);
	else if ($operation == 'remove')
		unassign_student($semester_id, $course_rn, $day_id, $start_time, $facilitator, $selected_id);

}
else
{
	if (isset($_GET['crn']) && isset($_GET['day']) && isset($_GET['time']) && isset($_GET['facilitator']))
	{
		$semester_id = get_default_semester();
		$course_rn = $_GET['crn'];
		$day_id = $_GET['day'];
		$start_time = $_GET['time'];
		$facilitator = $_GET['facilitator'];
		$data = get_class_time_by_crn($course_rn, $day_id, $start_time, $semester_id);
		if ($data !== false)
		{
			if (is_facilitator_booked($semester_id, $day_id, $start_time, $course_rn, $facilitator))
			{
				$end_time = $data['end_time'];
				$room_number = $data['room_number'];
				$students = array();
			}
			else
			{
				set_session_message("Facilitator is not booked in that class.");
				redirect('schedule-build.php');
			}
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
			form_back_button('schedule-class.php?crn='.urlencode($course_rn).'&day='.urlencode($day_id).'&time='.urlencode($start_time));
			form_read_only('semester_id', 'Semester', $semester_id);
			form_read_only('course_rn', 'CRN', $course_rn);
			form_read_only('room_number', 'Room', $room_number);
			form_read_only('day_id', 'Day', $day_id);
			form_read_only_time($start_time, $end_time);
			?>
		</ul>
		<ul>
			<?php form_read_only('facilitator', 'Facilitator', $facilitator); ?>
			<input id="selected_id" name="selected_id" type="hidden" value="" />
			<input id="operation" name="operation" type="hidden" value="" />
		</ul>
	</form>
	<?php
	$booked_students = get_assigned_students($semester_id, $course_rn, $day_id, $start_time, $facilitator);
	if (count($booked_students) > 0)
	{
		echo '<div class="search_results">';
			display_search_results('assigned-students-scheduling', $booked_students, $_GET);
		echo '</div>';
	}
	if (count_assigned_students($semester_id, $course_rn, $day_id, $start_time, $facilitator) < MAX_STUDENTS_PER_FACILITATOR)
	{
		$available_students = unassigned_students_by_class($semester_id, $course_rn, $day_id, $start_time);
		if (count($available_students) > 0)
		{
			echo '<h2>Available Students</h2>';
			echo '<div class="search_results">';
				display_search_results('unassigned-students-scheduling', $available_students, $_GET);
			echo '</div>';
		}
	}

include "modules/footer.php";
