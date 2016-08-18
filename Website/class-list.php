<?php
include_once "modules/constants.php";
$title = "List Classes For Course";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if (isset($_GET['code']))
{
	$course_code = $_GET['code'];
	$course_name = get_course_name($course_code);
	if ($course_name === false)
	{
		set_session_message("<em>".$course_code."</em> is not registered in the system.");
		redirect("course-list.php");
	}
}
else
{
	set_session_message("Please select a course.");
	redirect("course-list.php");
}
$semester_id = get_default_semester();
$all_classes = get_classes($course_code, $semester_id);

?>
	<h3><?php echo $course_code; ?></h3>
	<h4><?php echo $course_name; ?></h4>
	<?php display_search_results('classes', $all_classes); ?>
	<h4><a href="class-add.php?course=<?php echo urlencode($course_code); ?>">Add another class</a></h4>
<?php include "modules/footer.php";
