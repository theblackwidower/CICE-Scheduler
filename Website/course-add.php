<?php
include_once "modules/constants.php";
$title = "Add Course";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	require 'modules/processes/add-course.php';

	if ($result === true)
	{
		$result = "Course <em>".$new_course_code."</em> successfully added.";
		$course_code = "";
		$course_name = "";
	}
	echo "<h2>".$result."</h2>";
}
else
{
	$course_code = "";
	$course_name = "";
}
	form_open_post(); ?>
		<ul>
			<?php form_text_box('course_code', 'Course Code', $course_code); ?>
		</ul>
		<ul>
			<?php form_text_box('course_name', 'Course Name', $course_name); ?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
