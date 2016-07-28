<?php
$campus_id = $_POST["campus_id"];
$room_number = strtoupper(trim($_POST["room_number"]));

$result = "";

if ($campus_id == "")
	$result .= "Please select a campus.<br />";

if ($room_number == "")
	$result .= "Please enter a room number.<br />";

if ($result == "")
{
	$room_number = parse_room_number($room_number);
	if (room_exists($room_number))
		$result .= "Room number is already registered.<br />";
	else
	{
		$code = add_room($campus_id, $room_number);
		if ($code === true)
			$result = true;
		else
			$result = "Unknown error occured: ".$code;
	}
}
