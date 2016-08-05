<?php
include_once "modules/constants.php";
$title = "Facilitator Schedule";
$restrictionCode = ALL_USERS;
include "modules/header.php";

if (ROLE_ADMIN == get_logged_in_role() ||
		ROLE_DATA_ENTRY == get_logged_in_role())
{
	if (isset($_GET['email']))
	{
		if (ROLE_ADMIN == get_logged_in_role())
			$schedule_links = TT_LINK_SCHEDULE_EDIT;
		else
			$schedule_links = TT_LINK_NONE;

		$email = $_GET['email'];
		$semester_id = get_default_semester();

		$data = get_facilitator($email);
		if ($data !== false)
		{
			$first_name = $data['first_name'];
			$last_name = $data['last_name'];
			$schedule = get_facilitator_schedule($email, $semester_id);
		}
		else
		{
			set_session_message("<em>".$email."</em> is not registered as a facilitator.");
			redirect("facilitator-list.php");
		}
	}
	else
	{
		set_session_message("Please select a facilitator.");
		redirect("facilitator-list.php");
	}
}
else if (ROLE_FACILITATOR == get_logged_in_role())
{
	$schedule_links = TT_LINK_NONE;
	$email = get_logged_in_email();
	$semester_id = get_default_semester();

	$data = get_facilitator($email);
	if ($data !== false)
	{
		$first_name = $data['first_name'];
		$last_name = $data['last_name'];
		$schedule = get_facilitator_schedule($email, $semester_id);
	}
	else
	{
		set_session_message("You are not registered as a facilitator. Please contact administration.");
		redirect("login.php");
	}
}
else
{
	set_session_message("Access denied.");
	header("HTTP/1.0 401 Unauthorized");
	include '401.php';
	//close php document
	exit;
}
?>
	<h2><?php echo format_name($first_name, $last_name, NAME_FORMAT_FIRST_NAME_FIRST); ?><br />Full Schedule</h2>
	<h3><?php echo get_semester_date($semester_id); ?></h3>
	<?php echo build_timetable($schedule, $schedule_links); ?>
<?php include "modules/footer.php";
