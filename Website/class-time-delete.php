<?php
include_once "modules/constants.php";
$title = "Delete Class Time";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$semester_id = $_POST["semester_id"];
	$course_rn = $_POST["course_rn"];
	$room_number = $_POST["room_number"];
	$day_id = $_POST["day_id"];
	$start_time = $_POST["start_time"];
	$end_time = $_POST["end_time"];

	$code = delete_class_time($room_number, $day_id, $start_time, $end_time, $semester_id, $course_rn);
	if ($code === true)
	{
		set_session_message("Class time successfully deleted.");
		redirect("class-schedule.php?crn=".urlencode($course_rn));
	}
	else
		echo "<h2>Unknown error occured: ".$code."</h2>";
}
else
{
	if (isset($_GET['day']) && isset($_GET['time']) &&
			(isset($_GET['room']) || isset($_GET['crn'])))
	{
		$semester_id = get_default_semester();
		$day_id = $_GET['day'];
		$start_time = $_GET['time'];

		if (isset($_GET['crn']))
		{
			$course_rn = $_GET['crn'];
			$data = get_class_time_by_crn($course_rn, $day_id, $start_time, $semester_id);
			if ($data !== false)
			{
				$room_number = $data['room_number'];
				$end_time = $data['end_time'];
			}
		}
		else if (isset($_GET['room']))
		{
			$room_number = $_GET['room'];
			$data = get_class_time_by_room($room_number, $day_id, $start_time, $semester_id);
			if ($data !== false)
			{
				$course_rn = $data['course_rn'];
				$end_time = $data['end_time'];
			}
		}

		if ($data === false)
		{
			set_session_message("Selected class is not registered in the system.");
			redirect('course-list.php');
		}
	}
	else
	{
		set_session_message("Please select all required information.");
		redirect('course-list.php');
	}
}
?>
	<h3>Are you sure you want to remove this block?</h3>
	<?php form_open_post(); ?>
		<ul>
			<?php
			form_read_only('course_rn', 'CRN', $course_rn);
			form_read_only('semester_id', 'Semester ID', $semester_id);
			?>
		</ul>
		<ul>
			<?php
			form_read_only('day_id', 'Day', $day_id);
			form_read_only('room_number', 'Room Number', $room_number);
			?>
		</ul>
		<ul>
			<?php form_read_only_time($start_time, $end_time); ?>
		</ul>
		<ul>
			<?php form_question_buttons('class-schedule.php', array('crn' => $course_rn)); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
