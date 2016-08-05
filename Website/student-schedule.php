<?php
include_once "modules/constants.php";
$title = "Student Schedule";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if (isset($_GET['id']))
{
	if (ROLE_ADMIN == get_logged_in_role())
		$schedule_links = TT_LINK_SCHEDULE_EDIT_FROM_STUDENT;
	else
		$schedule_links = TT_LINK_NONE;

	$semester_id = get_default_semester();
	$student_id = $_GET['id'];

	$data = get_student($student_id);
	if ($data === false)
	{
		set_session_message("<em>".$course_rn."</em> is not registered in the system.");
		redirect('course-list.php');
	}
	$first_name = $data['first_name'];
	$last_name = $data['last_name'];
	$schedule = get_student_schedule($student_id, $semester_id);
	$registration_list = get_student_registration($student_id, $semester_id);
}
else
{
	set_session_message("Please select a student.");
	redirect('student-list.php');
}

?>
	<h2><?php echo format_name($first_name, $last_name, NAME_FORMAT_FIRST_NAME_FIRST); ?><br />Full Schedule</h2>
	<h3><?php echo get_semester_date($semester_id); ?></h3>
	<?php echo build_timetable($schedule, $schedule_links); ?>
	<?php echo display_registration_ul($registration_list, $student_id); ?>
	<h4><a href="student-schedule-add.php?id=<?php echo urlencode($student_id); ?>">Add Class</a></h4>
<?php include "modules/footer.php";
