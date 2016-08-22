<?php
require_once "../../modules/constants.php";
$restrictionCode = ROLE_DATA_ENTRY;
require_once "../../modules/init.php";

$search = trim($_GET["q"]);
$field = trim($_GET["f"]);

if ($field == 'professor')
{
	$display_name = 'Professor';
	$value_id = 'professor_id';
	$title_id = 'name';
	$subtitle_id = 'email';
	$all_items = search_professors($search, MAX_AUTOCOMPLETE_RESULT);
}
else if ($field == 'course')
{
	$display_name = 'Course';
	$value_id = 'course_code';
	$title_id = 'course_code';
	$subtitle_id = 'course_name';
	$all_items = search_courses($search, MAX_AUTOCOMPLETE_RESULT);
}
else if ($field == 'room')
{
	$display_name = 'Room';
	$value_id = 'room_number';
	$title_id = 'room_number';
	$subtitle_id = 'campus_name';
	$all_items = search_rooms($search, MAX_AUTOCOMPLETE_RESULT);
}
else if ($field == 'crn')
{
	$display_name = 'Class';
	$value_id = 'course_rn';
	$title_id = 'course_rn';
	$subtitle_id = 'course_code';
	$all_items = search_class_rn($search, get_default_semester(), MAX_AUTOCOMPLETE_RESULT);
}
else if ($field == 'student')
{
	$display_name = 'Student';
	$value_id = 'student_id';
	$title_id = 'student_id';
	$subtitle_id = 'name';
	$all_items = search_students($search, MAX_AUTOCOMPLETE_RESULT);
}
foreach ($all_items as $item)
	display_autocomplete_item($item, $value_id, $title_id, $subtitle_id);

echo '<a href="#" class="add_message"
			onclick="new_popup(\''.$field.'\', \'autocomplete/add-popup.php?f='.$field.'\');
			auto_complete_clear_selection(this.parentNode)
			close_auto_complete(this.parentNode.parentNode);
			return false;" onmouseover="auto_complete_mouse_selection(this)">Add<br />'.$display_name.'</a>';
