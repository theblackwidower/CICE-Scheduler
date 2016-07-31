<?php
include_once "modules/constants.php";
$title = "Add Class Time";
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

	$result = "";

	if ($semester_id == "" || !is_registered_semester($semester_id))
		$semester_id = get_default_semester();

	if ($course_rn == "" || !class_rn_exists($course_rn, $semester_id))
		$result .= "Please enter a valid CRN.<br />";

	if ($room_number == "" || !room_exists($room_number))
		$result .= "Please select a valid room number.<br />";

	if ($day_id == "" || !is_valid_day($day_id))
		$result .= "Please select a valid day.<br />";

	if (!ctype_digit($start_time) || !ctype_digit($end_time))
		$result .= "Time must be a number.<br />";
	else if ($start_time >= $end_time)
		$result .= "Start time must come before end time.<br />";
	else if ($end_time - $start_time > MAX_CLASS_LENGTH)
		$result .= "Class cannot be more than ".MAX_CLASS_LENGTH." hours long.<br />";

	$professor_id = get_class_rn($course_rn, $semester_id)['professor_id'];

	$room_check = is_room_booked($room_number, $day_id, $start_time, $end_time, $semester_id);
	$class_check = is_class_booked($course_rn, $day_id, $start_time, $end_time, $semester_id);
	$prof_check = is_prof_booked($professor_id, $day_id, $start_time, $end_time, $semester_id);

	if ($room_check !== false)
		$result .= "Room booked at that time by CRN <em>".$room_check."</em>.<br />";

	if ($class_check !== false)
		$result .= "Class busy at that time in room <em>".$class_check."</em>.<br />";

	if ($prof_check !== false)
		$result .= "Professor busy at that time for CRN <em>".$prof_check."</em>.<br />";

	if ($result == "")
	{
		$code = add_class_time($room_number, $day_id, $start_time, $end_time, $semester_id, $course_rn);
		if ($code === true)
		{
			$result = "Class time successfully added.";

			$room_number = "";
			$start_time = START_SCHEDULE;
			$end_time = END_SCHEDULE;
		}
		else
			$result = "Unknown error occured: ".$code;
	}

	echo "<h2>".$result."</h2>";
}
else
{
	if (isset($_GET['crn']))
	{
		$semester_id = get_default_semester();
		$course_rn = $_GET['crn'];
		if (class_rn_exists($course_rn, $semester_id))
		{
			$room_number = "";
			$day_id = "";
			$start_time = START_SCHEDULE;
			$end_time = START_SCHEDULE + MAX_CLASS_LENGTH;
		}
		else
		{
			set_session_message("<em>".$course_rn."</em> is not registered in the system.");
			redirect('course-list.php');
		}
	}
	else
	{
		set_session_message("Please select a class.");
		redirect('course-list.php');
	}
}
popup_casing('room');
	form_open_post(); ?>
		<ul>
			<?php
			form_back_button('class-schedule.php?crn='.urlencode($course_rn));
			form_read_only('course_rn', 'CRN', $course_rn);
			form_read_only('semester_id', 'Semester ID', $semester_id);
			?>
		</ul>
		<ul>
			<?php
			form_drop_down_box('day_id', 'Day', $day_id);
			form_autocomplete_box('room_number', 'Room Number', 'room', $room_number);
			?>
		</ul>
		<ul>
			<?php
			form_time_ddl($start_time, $end_time);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
