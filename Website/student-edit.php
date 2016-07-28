<?php
include_once "modules/constants.php";
$title = "Edit Student";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$student_id = trim($_POST["student_id"]);
	$first_name = trim($_POST["first_name"]);
	$last_name = trim($_POST["last_name"]);
	$is_active = isset($_POST["is_active"])?'true':'false';

	$result = "";

	if ($first_name == "")
		$result .= "Please enter a first name.<br />";

	if ($last_name == "")
		$result .= "Please enter a last name.<br />";


	if ($result == "")
	{
		$code = update_student($student_id, $first_name, $last_name, $is_active);
		if ($code === true)
		{
			set_session_message("Student <em>".format_name($first_name, $last_name, NAME_FORMAT_FIRST_NAME_FIRST)."</em> successfully updated.");
			redirect("student-list.php");
		}
		else
			$result = "Unknown error occured: ".$code;
	}

	echo "<h2>".$result."</h2>";
}
else
{
	if (isset($_GET['id']))
	{
		$student_id = $_GET['id'];
		$student = get_student($student_id);
		if ($student !== false)
		{
			$first_name = $student['first_name'];
			$last_name = $student['last_name'];
			$is_active = $student['is_active']?'true':'false';
		}
		else
		{
			set_session_message("<em>".$student_id."</em> is not registered as a student.");
			redirect("student-list.php");
		}
	}
	else
	{
		set_session_message("Please select a student.");
		redirect("student-list.php");
	}
}
	form_open_post(); ?>
		<ul>
			<?php
			form_read_only('student_id', 'Student ID', $student_id);
			form_checkbox('is_active', 'Active', $is_active);
			?>
		</ul>
		<ul>
			<?php
			form_text_box('first_name', 'First Name', $first_name);
			form_text_box('last_name', 'Last Name', $last_name);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
