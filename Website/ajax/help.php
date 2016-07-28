<?php
require_once "../modules/constants.php";
$restrictionCode = PUBLIC_ACCESS;
require_once "../modules/init.php";

$file = trim($_GET["f"]);

switch ($file)
{
	case 'administration.php':
		?>
		<h3>Administration Settings</h3>
		<h4>Set Default Semester</h4>
		<p>
			The default semester is the semester which the site currently operates within. By changing the value,
			you'll be able to edit class and schedule information for a different semester.
		</p>
		<p>
			Logged in facilitators will only be able to view their schedules for the current semester.
		</p>
		<?php if (get_logged_in_role() == ROLE_ADMIN) {?>
			<h4>Add New Semester</h4>
			<p>
				If a semester is not identified in the system, it has to be manually added.
			</p>
			<p>
				Semester code should be defined by the following rules: Fall semesters start with an 'F', and
				winter semesters start with a 'W', followed by the four-digit year. Though this is not enforced
				by the system itself, and one can choose to define a semester by whatever rules they want.
				However following these rules will ensure consistency, and will make entry of the start and
				end dates slightly easier.
			</p>
			<p>
				The start date and end date are crucial to the overall operation of the system. It will be
				displayed when a facilitator views their schedule, and will allow the system to know which
				semester is 'current' so the facilitators view the right schedule.
			</p>
			<p>
				If the semester code is built using the above rules, the year and month will be entered
				automatically. The actual day number however, will need to be entered manually. The start date
				should be defined as the first day classes start, and the end date should be the last day normal
				classes run. This does not include exams.
			</p>
			<h4>Add New Campus</h4>
			<p>
				This feature is only necessary if a new campus opens and is unlikely to be used for any other
				purpose.
			</p>
			<p>
				Be sure to give the campus a simple name. The campus code must be a single letter, and is
				recommended to be the first letter of the campus name.
			</p>
			<h4>Database Maintenance</h4>
			<p>
				There are two basic features in Database Maintenance.
			</p>
			<h5>Backups</h5>
			<p>
				Database backups should be performed regularly. In most frequently-used systems, a database
				should be backed-up daily, or weekly. In an infrequently-used systems, where things are changing
				rarely, such as this one, that won't be necessary.
			</p>
			<p>
				Database backups are recommended to be performed regularly only when a lot of information is being
				entered, and before and after every semester. Just remember, in the event of a system failure,
				anything entered in the system since the last backup will need to be redone.
			</p>
			<p>
				These backup files can be saved to your hard drive, and contain all data within the system.
			</p>
			<h5>Vacuum</h5>
			<p>
				The 'Vacuum' and 'Full Vacuum' buttons allow for a maintenance function that can be used to free
				space, and speed up the system overall.
			</p>
			<p>
				A basic vacuum frees up space that has been left behind by deleted records. It also updates the
				database's own internal census, and allows it to operate more efficiently.
			</p>
			<p>
				A full vacuum also frees up space left behind by deleted records, but is much more through, and
				therefore more time-consuming than the basic vacuum. While a full vacuum is running, no data
				within the system can be changed, which is why it should only be run when no one else is using it.
			</p>
			<p>
				A basic vacuum can be run once a week to clean up the internals of the system. A full vacuum
				should be run much more sparingly, around once or twice a year.
			</p>
			<p>
				Neither operation is mandatory, as the database software itself should run it's own vacuum
				automatically when necessary. But if it doesn't, it can be a good idea to run it on your own.
			</p>
		<?php }
		break;

	case 'class-add.php':
		?>
		<h3>Add Class Block</h3>
		<p>
			Enter all information to create a new class block. A class block is not the same as a course. This
			is one class taught by a single professor.
		</p>
		<p>
			Be aware that neither course code nor CRN can be changed later.
		</p>
		<p>
			If this class is taught by a facilitator, ensure that the facilitator is also listed as a professor,
			with the same email address, and added as a professor to the class block, to prevent schedule
			conflicts.
		</p>
		<?php
		break;

	case 'class-edit.php':
		?>
		<h3>Edit Class Block</h3>
		<p>
			Here you can assign a different professor to a class.
		</p>
		<p>
			Be aware that if the new professor is also a facilitator, and this assignment creates conflicts in
			their schedule, most facilitation conflicts will be deleted automatically, which might require new
			facilitators to be assigned as needed.
		</p>
		<?php
		break;

	case 'class-list.php':
		?>
		<h3>List Classes For Course</h3>
		<p>
			Here you will see every class block for a course. From here you can change which professor is assigned
			to the class, or view the class schedule.
		</p>
		<p>
			Add a new class block for this course by clicking the link at the bottom.
		</p>
		<?php
		break;

	case 'class-schedule.php':
		?>
		<h3>Class Schedule</h3>
		<p>

		</p>
		<?php
		break;

	case 'class-time-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'class-time-delete.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'course-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'course-edit.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'course-inactive.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'course-list.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'facilitator-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'facilitator-edit.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'facilitator-inactive.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'facilitator-list.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'facilitator-schedule.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'index.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'login.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'professor-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'professor-edit.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'professor-inactive.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'professor-list.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'room-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'room-list.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-build.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-class.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-list.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-students.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-edit.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-inactive.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-list.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-register.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-schedule-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-schedule-delete.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-schedule.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-search.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-add.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-change-email.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-change-password.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-list.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-password-reset.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-role.php':
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case '400.php':
		?>
		<h3>400 Error</h3>
		<p>
			Your browser sent a bad request. Press reload or F5 to try again.
		</p>
		<?php
		break;

	case '401.php':
		?>
		<h3>401 Error</h3>
		<p>
			You attempted to access a page you are not authorized to access. If you think you should
			be able to access this page, you may not be using the right user account. To fix this, log out and
			log back in. If you're still experiencing problems, contact server administration.
		</p>
		<?php
		break;

	case '403.php':
		?>
		<h3>403 Error</h3>
		<p>
			You attempted to access a resource you are not authorized to access, likely a directory listing.
			If you think this is in error, contact server administration.
		</p>
		<?php
		break;

	case '404.php':
		?>
		<h3>404 Error</h3>
		<p>
			File not found. You might have typed in a non-existant URL, or clicked a broken link.
		</p>
		<?php
		break;

	case '500.php':
		?>
		<h3>500 Error</h3>
		<p>
			Internal error. Contact server administration.
		</p>
		<?php
		break;

	case '503.php':
		?>
		<h3>503 Error</h3>
		<p>
			The database might be down. Contact server administration.
		</p>
		<?php
		break;

	default:

		?>
		<h3>No Help File Found</h3>
		<p>
			<?php echo $file; ?>
		</p>
		<?php
}
