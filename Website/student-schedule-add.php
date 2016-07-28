<?php
include_once "modules/constants.php";
$title = "Register Student for Class";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$student_id = trim($_POST["student_id"]);
	$semester_id = trim($_POST["semester_id"]);
	$course_rn = trim($_POST["course_rn"]);

	$result = "";

	if ($student_id == "" || !student_exists($student_id))
		$result .= "Please enter a valid student id.<br />";

	if ($semester_id == "" || !is_registered_semester($semester_id))
		$semester_id = get_default_semester();

	if ($course_rn == "" || !class_rn_exists($course_rn, $semester_id))
		$result .= "Please enter a valid course code.<br />";

	if ($result == "")
	{
		if (student_registration_exists($student_id, $semester_id, $course_rn))
			$result = "Student is already registered for <em>".$course_rn."</em>.";
		else
		{
			$code = add_student_registration($student_id, $semester_id, $course_rn);
			if ($code === true)
			{
				$result = "Student successfully registered for <em>".$course_rn."</em>.";
				$course_rn = '';
			}
			else
				$result = "Unknown error occured: ".$code;
		}
	}

	echo "<h2>".$result."</h2>";
}
else
{
	if (isset($_GET['id']))
	{
		$student_id = $_GET['id'];
		$semester_id = get_default_semester();
		$course_rn = '';

		if (!student_exists($student_id))
		{
			set_session_message("<em>".$student_id."</em> is not registered in the system.");
			redirect('student-search.php');
		}
	}
	else
	{
		set_session_message("Please select a student.");
		redirect('student-search.php');
	}
}
popup_casing('crn');
	form_open_post(); ?>
		<ul>
			<?php
			form_back_button('student-schedule.php?id='.urlencode($student_id));
			form_read_only('student_id', 'Student ID', $student_id);
			form_read_only('semester_id', 'Semester ID', $semester_id);
			?>
		</ul>
		<ul>
			<?php form_autocomplete_box('course_rn', 'CRN', 'crn', $course_rn); ?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_REGISTER); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
