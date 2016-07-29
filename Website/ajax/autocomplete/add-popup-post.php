<?php
require_once "../../modules/constants.php";
$restrictionCode = ROLE_DATA_ENTRY;
require_once "../../modules/init.php";

header('Content-type: application/json');

$output = Array();
$output["field"] = trim($_POST["field"]);
$output["message"] = "";

if ($output["field"] == 'professor')
{
	require '../../modules/processes/add-professor.php';

	$id = $professor_id;
	$display_name = get_professor_name($professor_id, NAME_FORMAT_LAST_NAME_FIRST);
}
else if ($output["field"] == 'course')
{
	require '../../modules/processes/add-course.php';

	$id = $course_code;
	$display_name = $course_code;
}
else if ($output["field"] == 'room')
{
	require '../../modules/processes/add-room.php';

	$id = $room_number;
	$display_name = $room_number;
}
else if ($output["field"] == 'crn')
{
	require '../../modules/processes/add-class.php';

	$id = $course_rn;
	$display_name = $course_rn;
}

if ($result === true)
{
	$output["success"] = true;
	$output["id"] = $id;
	$output["display"] = $display_name;
}
else
{
	$output["success"] = false;
	$output["message"] = $result;
}

echo json_encode($output);
