<?php
include_once "modules/constants.php";
$title = "Add Student";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	require 'modules/processes/add-student.php';

	if ($result === true)
	{
		$result = "Student <em>".format_name($first_name, $last_name, NAME_FORMAT_FIRST_NAME_FIRST)."</em> successfully added.";
		$student_id = "";
		$first_name = "";
		$last_name = "";
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
