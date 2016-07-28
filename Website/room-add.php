<?php
include_once "modules/constants.php";
$title = "Add Room";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	require 'modules/processes/add-room.php';

	if ($result === true)
	{
		$result = "Room <em>".$room_number."</em> successfully added.";
		$room_number = "";
	}
	echo "<h2>".$result."</h2>";
}
else
{
	$campus_id = "";
	$room_number = "";
}
	form_open_post(); ?>
		<ul>
			<?php
			form_drop_down_box('campus_id', 'Campus', $campus_id);
			form_text_box('room_number', 'Room Number', $room_number);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
