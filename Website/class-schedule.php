<?php
include_once "modules/constants.php";
$title = "Class Schedule";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if (isset($_GET['crn']))
{
	$semester_id = get_default_semester();
	$course_rn = $_GET['crn'];

	$data = get_class_rn($course_rn, $semester_id);
	if ($data === false)
	{
		set_session_message("<em>".$course_rn."</em> is not registered in the system.");
		redirect('course-list.php');
	}
	$course_code = $data['course_code'];
	$professor_id = $data['professor_id'];
	$schedule = get_class_schedule($course_rn, $semester_id);
}
else
{
	set_session_message("Please select a class.");
	redirect('course-list.php');
}

?>
	<h3><a href="class-list.php?code=<?php echo urlencode($course_code); ?>"><?php echo $course_code; ?></a></h3>
	<h4><?php echo get_course_name($course_code).(($professor_id != "")?'<br />Taught by: '.get_professor_name($professor_id, NAME_FORMAT_FIRST_NAME_FIRST):''); ?></h4>
	<?php echo build_timetable($schedule, TT_LINK_CLASS_TIME_DELETE); ?>
	<h4><a href="class-time-add.php?crn=<?php echo urlencode($course_rn); ?>">Add class time</a></h4>
<?php include "modules/footer.php";
