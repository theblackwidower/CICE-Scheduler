<?php
include_once "modules/constants.php";
$title = "Edit Course";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$course_code = trim($_POST["course_code"]);
	$course_name = trim($_POST["course_name"]);
	$is_active = isset($_POST["is_active"])?'true':'false';

	$result = "";

	if ($course_name == "")
		$result .= "Please enter a course name.";

	if ($result == "")
	{
		$code = update_course($course_code, $course_name, $is_active);
		if ($code === true)
		{
			set_session_message("Course <em>".$course_code."</em> successfully updated.");
			redirect("course-list.php");
		}
		else
			$result = "Unknown error occured: ".$code;
	}

	echo "<h2>".$result."</h2>";
}
else
{
	if (isset($_GET['code']))
	{
		$course_code = $_GET['code'];
		$course = get_course($course_code);
		if ($course !== false)
		{
			$course_name = $course['course_name'];
			$is_active = $course['is_active']?'true':'false';
		}
		else
		{
			set_session_message("<em>".$course_code."</em> is not registered in the system.");
			redirect("course-list.php");
		}
	}
	else
	{
		set_session_message("Please select a course.");
		redirect("course-list.php");
	}
}
	form_open_post(); ?>
		<ul>
			<?php
			form_read_only('course_code', 'Course Code', $course_code);
			form_checkbox('is_active', 'Active', $is_active);
			?>
		</ul>
		<ul>
			<?php form_text_box('course_name', 'Course Name', $course_name); ?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
