<?php
require_once "../../modules/constants.php";
$restrictionCode = ROLE_DATA_ENTRY;
require_once "../../modules/init.php";

$field = trim($_GET["f"]);

if ($field == 'professor')
	echo '<h2 class="popup_message">Add Professor</h2>';
else if ($field == 'course')
	echo '<h2 class="popup_message">Add Course</h2>';
else if ($field == 'room')
	echo '<h2 class="popup_message">Add Room</h2>';
else if ($field == 'crn')
{
	echo '<h2 class="popup_message">Add Class Block</h2>';
	popup_casing('course');
	popup_casing('professor');
}
?>

<form onsubmit="ajax_submit(this); return false;" onreset="reset_form(this); return false;">
	<input name="field" id="field" type="hidden" value="<?php echo $field; ?>" />
	<ul>
		<?php
		if ($field == 'professor')
		{
			form_text_box('first_name', 'First Name', '');
			form_text_box('last_name', 'Last Name', '');
			echo '</ul><ul>';
			form_text_box('email', 'Email Address (optional)', '');
		}
		else if ($field == 'course')
		{
			form_text_box('course_code', 'Course Code', '');
			echo '</ul><ul>';
			form_text_box('course_name', 'Course Name', '');
		}
		else if ($field == 'room')
		{
			form_drop_down_box('campus_id', 'Campus', '');
			form_text_box('room_number', 'Room Number', '');
		}
		else if ($field == 'crn')
		{
			form_autocomplete_box('course_code', 'Course Code', 'course', '');
			form_read_only('semester_id', 'Semester ID', get_default_semester());
			echo '</ul><ul>';
			form_text_box('course_rn', 'CRN', '');
			form_autocomplete_box('professor_id', 'Professor (optional)', 'professor', '');
		}
		?>
	</ul>
	<ul>
		<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
	</ul>
</form>
