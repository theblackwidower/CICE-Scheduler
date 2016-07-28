<?php
include_once "modules/constants.php";
$title = "Add Class Block";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	require 'modules/processes/add-class.php';

	if ($result === true)
	{
		$result = "CRN <em>".$course_rn."</em> successfully added.<br />";
		$result .= '<a href="class-schedule.php?crn='.urlencode($course_rn).'">Schedule View.</a>';

		$course_rn = "";
		if (!isset($_GET['course']))
			$course_code = "";
		$professor_id = "";
	}
	echo "<h2>".$result."</h2>";
}
else
{
	$semester_id = get_default_semester();
	$course_rn = "";

	if (isset($_GET['course']))
		$course_code = $_GET['course'];
	else
		$course_code = "";

	$professor_id = "";
}
popup_casing('course');
popup_casing('professor');
	form_open_post(); ?>
		<ul>
			<?php
			if (isset($_GET['course']))
			{
				form_back_button('class-list.php?code='.urlencode($course_code));
				form_read_only('course_code', 'Course Code', $course_code);
			}
			else
				form_autocomplete_box('course_code', 'Course Code', 'course', $course_code);

			form_read_only('semester_id', 'Semester ID', $semester_id);
			?>
		</ul>
		<ul>
			<?php
			form_text_box('course_rn', 'CRN', $course_rn);
			form_autocomplete_box('professor_id', 'Professor (optional)', 'professor', $professor_id);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
