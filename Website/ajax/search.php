<?php
require_once "../modules/constants.php";
$restrictionCode = ROLE_DATA_ENTRY;
require_once "../modules/init.php";

$field = trim($_GET["f"]);
$search = trim($_GET["q"]);

if ($field == 'users')
	$all_items = search_users($search);
else if ($field == 'facilitators')
	$all_items = search_facilitators($search);
else if ($field == 'inactive-facilitators')
	$all_items = search_inactive_facilitators($search);
else if ($field == 'professors')
	$all_items = search_professors($search);
else if ($field == 'inactive-professors')
	$all_items = search_inactive_professors($search);
else if ($field == 'students')
	$all_items = search_students($search);
else if ($field == 'unregistered-students')
	$all_items = search_unregistered_students(get_default_semester(), $search);
else if ($field == 'registered-students')
	$all_items = search_registered_students(get_default_semester(), $search);
else if ($field == 'inactive-students')
	$all_items = search_inactive_students($search);
else if ($field == 'courses')
	$all_items = search_courses($search);
else if ($field == 'inactive-courses')
	$all_items = search_inactive_courses($search);

if (ends_with($field, 'facilitators'))
	$field = 'facilitators';
else if (ends_with($field, 'professors'))
	$field = 'professors';
else if (ends_with($field, 'students'))
	$field = 'students';
else if (ends_with($field, 'courses'))
	$field = 'courses';

display_search_results($field, $all_items);
