<?php
$semester_id = trim($_POST["semester_id"]);
$course_rn = trim($_POST["course_rn"]);
$course_code = trim($_POST["course_code"]);
$professor_id = trim($_POST["professor_id"]);

$result = "";

if ($semester_id == "" || !is_registered_semester($semester_id))
	$semester_id = get_default_semester();

if ($course_code == "" || !course_exists($course_code))
	$result .= "Please enter a valid course code.<br />";

if (trim($_POST["professor_search"]) == "")
	$professor_id = null;
else if ($professor_id == "" || !professor_exists($professor_id))
	$result .= "Please select a valid professor.<br />";

if ($course_rn == "")
	$result .= "Please enter a valid CRN.<br />";
else if (!ctype_digit($course_rn))
	$result .= "CRN must be numeric.<br />";
else if (strlen($course_rn) < 5)
	$result .= "CRN must have 5 numbers.<br />";

if ($result == "")
{
	if (class_rn_exists($course_rn, $semester_id))
		$result = "CRN is already assigned for the current semester.";
	else
	{
		$code = add_class_rn($semester_id, $course_rn, $course_code, $professor_id);
		if ($code === true)
			$result = true;
		else
			$result = "Unknown error occured: ".$code;
	}
}
