<?php
include_once "modules/constants.php";
$title = "Add Student";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$student_id = trim($_POST["student_id"]);
	$first_name = trim($_POST["first_name"]);
	$last_name = trim($_POST["last_name"]);

	$result = "";

	if ($student_id == "")
		$result .= "Please enter a student_id.<br />";
	else if (!is_numeric($student_id))
		$result .= 'Student ID must be a number.<br />';
	else if (strlen($student_id) != 9)
		$result .= 'Student ID must be 9 characters.<br />';

	if ($first_name == "")
		$result .= "Please enter a first name.<br />";

	if ($last_name == "")
		$result .= "Please enter a last name.<br />";


	if ($result == "")
	{
		if (student_exists($student_id))
			$result = "Student ID is already registered.";
		else
		{
			$code = add_student($student_id, $first_name, $last_name);
			if ($code === true)
			{
				$result = "Student <em>".format_name($first_name, $last_name, NAME_FORMAT_FIRST_NAME_FIRST)."</em> successfully added.";
				$student_id = "";
				$first_name = "";
				$last_name = "";
			}
			else
				$result = "Unknown error occured: ".$code;
		}
	}

	echo "<h2>".$result."</h2>";
}
else
{
	$student_id = '';
	$first_name = '';
	$last_name = '';
}
	form_open_post(); ?>
		<ul>
			<?php form_text_box('student_id', 'Student ID', $student_id); ?>
		</ul>
		<ul>
			<?php
			form_text_box('first_name', 'First Name', $first_name);
			form_text_box('last_name', 'Last Name', $last_name);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
