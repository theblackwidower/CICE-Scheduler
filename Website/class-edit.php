<?php
include_once "modules/constants.php";
$title = "Edit Class Block";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$semester_id = trim($_POST["semester_id"]);
	$course_rn = trim($_POST["course_rn"]);
	$course_code = trim($_POST["course_code"]);
	$professor_id = trim($_POST["professor_id"]);

	$result = "";

	if (trim($_POST["professor_search"]) == "")
		$professor_id = null;
	else if ($professor_id == "" || !professor_exists($professor_id))
		$result .= "Please select a valid professor.<br />";

	if ($result == "")
	{
		$code = update_class_rn($semester_id, $course_rn, $professor_id);
		if ($code === true)
		{
			set_session_message("CRN <em>".$course_rn."</em> successfully updated.");
			redirect("class-list.php?code=".urlencode($course_code));
		}
		else
			$result = "Unknown error occured: ".$code.'<br />';
	}

	echo "<h2>".$result."</h2>";
}
else
{
	if (isset($_GET['crn']))
	{
		$semester_id = get_default_semester();
		$course_rn = $_GET['crn'];

		$class = get_class_rn($course_rn, $semester_id);
		if ($class === false)
		{
			set_session_message("<em>".$course_rn."</em> is not registered in the system.");
			redirect('course-list.php');
		}
		else
		{
			$professor_id = $class['professor_id'];
			$course_code = $class['course_code'];
		}
	}
	else
	{
		set_session_message("Please select a class.");
		redirect('course-list.php');
	}
}
popup_casing('professor');
	form_open_post(); ?>
		<ul>
			<?php
			form_read_only('course_code', 'Course Code', $course_code);
			form_read_only('semester_id', 'Semester ID', $semester_id);
			?>
		</ul>
		<ul>
			<?php
			form_read_only('course_rn', 'CRN', $course_rn);
			form_autocomplete_box('professor_id', 'Professor (optional)', 'professor', $professor_id);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
