<?php
$course_code = strtoupper(trim($_POST["course_code"]));
$course_name = trim($_POST["course_name"]);

$result = "";

if ($course_code == "")
	$result .= "Please enter a course code.<br />";

if ($course_name == "")
	$result .= "Please enter a course name.<br />";

if ($result == "")
{
	$course_code = parse_course_code($course_code);
	if ($course_code === false)
		$result = "Invalid course code entered. Must have no more than five letters, followed by no more than five numbers.";
	else
	{
		if (course_exists($course_code))
			$result = "Course code is already registered.";
		else
		{
			$code = add_course($course_code, $course_name);
			if ($code === true)
				$result = true;
			else
				$result = "Unknown error occured: ".$code;
		}
	}
}
