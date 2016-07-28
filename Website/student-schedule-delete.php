<?php
include_once "modules/constants.php";
$title = "Drop Student from Class";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$student_id = $_POST["student_id"];
	$semester_id = $_POST["semester_id"];
	$course_rn = $_POST["course_rn"];

	$code = delete_student_registration($student_id, $semester_id, $course_rn);
	if ($code === true)
	{
		set_session_message("Class successfully removed from student record.");
		redirect("student-schedule.php?id=".urlencode($student_id));
	}
	else
		echo "<h2>Unknown error occured: ".$code."</h2>";
}
else
{
	if (isset($_GET['id']))
	{
		$student_id = $_GET['id'];
		if (!student_exists($student_id))
		{
			set_session_message("Please select a valid student.");
			redirect('student-search.php');
		}
		else if (isset($_GET['crn']))
		{
			$semester_id = get_default_semester();
			$course_rn = $_GET['crn'];

			if (!student_registration_exists($student_id, $semester_id, $course_rn))
			{
				set_session_message("<em>".$student_id."</em> is not registered for this class.");
				redirect('student-schedule.php?id='.$student_id);
			}
		}
		else
		{
			set_session_message("Please select a crn.");
			redirect('student-schedule.php?id='.$student_id);
		}
	}
	else
	{
		set_session_message("Please select a student.");
		redirect('student-search.php');
	}
}
?>
	<h3>Are you sure you want to remove this student's registration?</h3>
	<?php form_open_post(); ?>
		<ul>
			<?php
			form_read_only('student_id', 'Student ID', $student_id);
			form_read_only('semester_id', 'Semester ID', $semester_id);
			?>
		</ul>
		<ul>
			<?php form_read_only('course_rn', 'CRN', $course_rn); ?>
		</ul>
		<ul>
			<?php form_question_buttons('student-schedule.php', array('id' => $student_id)); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
