<?php
include_once "modules/constants.php";
$title = "List Rooms";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
$all_campuses = get_all_campuses();
foreach ($all_campuses as $campus)
{
	echo '<div class="room_list">';
	echo $campus['campus_name'];
	echo '<ul>';
	$all_rooms = get_all_rooms_on_campus($campus['campus_id']);
	foreach ($all_rooms as $room)
		echo display_room_li($room);
	echo '</ul>';
	echo '</div>';
}
include "modules/footer.php";
