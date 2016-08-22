<?php
$student_id = trim($_POST["student_id"]);
$first_name = trim($_POST["first_name"]);
$last_name = trim($_POST["last_name"]);

$result = "";

if ($student_id == "")
	$result .= "Please enter a student id.<br />";
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
			$result = true;
		else
			$result = "Unknown error occured: ".$code;
	}
}
