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
			The default semester is the semester which the site currently operates
			within. By changing the value, you'll be able to edit class and schedule
			information for a different semester.
		</p>
		<p>
			Logged in facilitators will only be able to view their schedules for the
			current semester.
		</p>
		<?php if (get_logged_in_role() == ROLE_ADMIN) {?>
			<h4>Add New Semester</h4>
			<p>
				If a semester is not identified in the system, it has to be manually
				added.
			</p>
			<p>
				Semester code should be defined by the following rules: Fall semesters
				start with an 'F', and winter semesters start with a 'W', followed by
				the four-digit year. Though this is not enforced by the system itself,
				and one can choose to define a semester by whatever rules they want.
				However following these rules will ensure consistency, and will make
				entry of the start and end dates slightly easier.
			</p>
			<p>
				The start date and end date are crucial to the overall operation of the
				system. It will be displayed when a facilitator views their schedule,
				and will allow the system to know which semester is 'current' so the
				facilitators view the right schedule.
			</p>
			<p>
				If the semester code is built using the above rules, the year and month
				will be entered automatically. The actual day number however, will need
				to be entered manually. The start date should be defined as the first
				day classes start, and the end date should be the last day normal
				classes run. This does not include exams.
			</p>
			<h4>Add New Campus</h4>
			<p>
				This feature is only necessary if a new campus opens and is unlikely to
				be used for any other purpose.
			</p>
			<p>
				Be sure to give the campus a simple name. The campus code must be a
				single letter, and is recommended to be the first letter of the campus
				name.
			</p>
			<h4>Database Maintenance</h4>
			<p>
				There are two basic features in Database Maintenance.
			</p>
			<h5>Backups</h5>
			<p>
				Database backups should be performed regularly. In most frequently-used
				systems, a database should be backed-up daily, or weekly. In an
				infrequently-used systems, where things are changing rarely, such as
				this one, that won't be necessary.
			</p>
			<p>
				Database backups are recommended to be performed regularly only when a
				lot of information is being entered, and before and after every
				semester. Just remember, in the event of a system failure, anything
				entered in the system since the last backup will need to be redone.
			</p>
			<p>
				These backup files can be saved to your hard drive, and contain all data
				within the system.
			</p>
			<h5>Vacuum</h5>
			<p>
				The 'Vacuum' and 'Full Vacuum' buttons allow for a maintenance function
				that can be used to free space, and speed up the system overall.
			</p>
			<p>
				A basic vacuum frees up space that has been left behind by deleted
				records. It also updates the database's own internal census, and allows
				it to operate more efficiently.
			</p>
			<p>
				A full vacuum also frees up space left behind by deleted records, but is
				much more through, and therefore more time-consuming than the basic
				vacuum. While a full vacuum is running, no data within the system can be
				changed, which is why it should only be run when no one else is using
				it.
			</p>
			<p>
				A basic vacuum can be run once a week to clean up the internals of the
				system. A full vacuum should be run much more sparingly, around once or
				twice a year.
			</p>
			<p>
				Neither operation is mandatory, as the database software itself should
				run it's own vacuum automatically when necessary. But if it doesn't, it
				can be a good idea to run it on your own.
			</p>
		<?php }
		break;

	case 'class-add.php':
		?>
		<h3>Add Class Block</h3>
		<p>
			Enter all information to create a new class block. A class block is not
			the same as a course. This is one class taught by a single professor.
		</p>
		<p>
			Be aware that neither course code nor CRN can be changed later.
		</p>
		<p>
			If this class is taught by a facilitator, ensure that the facilitator is
			also listed as a professor, with the same email address, and added as a
			professor to the class block, to prevent schedule conflicts.
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
			The new professor must have their timetable free during the same time
			blocks currently occupied by the class. If there is a conflict, you will
			be alerted to it, and might need to delete the time conflicts before
			continuing.
		</p>
		<p>
			Be aware that if the new professor is also a facilitator, and this
			assignment creates conflicts in their schedule, most facilitation
			conflicts will be deleted automatically, which might require new
			facilitators to be assigned as needed.
		</p>
		<?php
		break;

	case 'class-list.php':
		?>
		<h3>List Classes For Course</h3>
		<p>
			Here you will see every class block for a course. From here you can change
			which professor is assigned to the class, or view the class schedule.
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
			Here, every class meeting time for a particular class block is displayed
			in a convienient timetable, with locations.
		</p>
		<p>
			A new meeting time can be added by clicking on the link at the bottom of
			the page.
		</p>
		<p>
			If a particular class time has been rescheduled or dropped, you can delete
			the old time by clicking the 'Delete' link in the timetable, and adding
			the new time if necessary. There is no way to edit a class time.
		</p>
		<?php
		break;

	case 'class-time-add.php':
		?>
		<h3>Add Class Time</h3>
		<p>
			From here, one can add a new meeting time for a particular class block.
		</p>
		<p>
			Select the day, the room number, and the start and end times for the
			class, and click the 'Create' button to submit.
		</p>
		<p>
			Classes cannot end before they start, and must be at least 1 hour long.
			They also cannot be longer than <?php echo MAX_CLASS_LENGTH; ?> hours.
		</p>
		<p>
			Conflicts may also arise if the class, room or professor is already booked
			at that time. These conflicts must be resolved before adding the new class
			time.
		</p>
		<p>
			If this class is taught by a facilitator and the new class block will
			cause a scheduling conflict, the system will react dynamically to prevent
			the most problematic issues. Be sure if the class is taught by a
			facilitator to check to see if any minor problems remain within their
			schedule.
		</p>
		<?php
		break;

	case 'class-time-delete.php':
		?>
		<h3>Delete Class Time</h3>
		<p>
			Click 'Yes' to remove the class block. Click 'No' to go back.
		</p>
		<?php
		break;

	case 'course-add.php':
		?>
		<h3>Add Course</h3>
		<p>
			Here, you can add a new course to the system.
		</p>
		<p>
			Be aware that a course differs from a class block or CRN. The course
			defines multiple classes with a shared subject matter. Individual classes
			are assigned individual professors, but a course will likely have multiple
			professors teaching it.
		</p>
		<p>
			Enter the course code and course name, then click 'Create' to add a new
			course.
		</p>
		<p>
			Course codes must follow a very specific format. The first half can
			contain only letters, and the second half must start with a number. Each
			half cannot be more than 5 characters.
		</p>
		<?php
		break;

	case 'course-edit.php':
		?>
		<h3>Edit Course</h3>
		<p>
			From here you can change a course name, or deactivate the course by
			deselecting the 'Active' checkbox, and clicking 'Update.'
		</p>
		<p>
			If a course is deactivated, it will no longer show up in search, or form
			autocomplete sections. Only deactivate a course if it is no longer being
			made available for registration in the current semester.
		</p>
		<p>
			If you wish to reactivate a course, just select the 'Active' checkbox, and
			click 'Update.'
		</p>
		<?php
		break;

	case 'course-inactive.php':
		?>
		<h3>Inactive Courses</h3>
		<p>
			Here, all inactive courses are listed.
		</p>
		<p>
			If you wish to reactivate a deactivated course, click on 'Edit Course' and
			select the 'Active' checkbox before clicking 'Update.'
		</p>
		<?php
		break;

	case 'course-list.php':
		?>
		<h3>List Current Courses</h3>
		<p>
			All courses that are currently active are listed here.
		</p>
		<p>
			You can search through the list by typing a term in the search box. This
			will search by course code and course name, displaying the first
			<?php echo MAX_SEARCH_RESULT; ?> results.
		</p>
		<p>
			You can also scroll through, page by page,
			<?php echo MAX_RESULTS_PER_PAGE; ?> results at a time.
		</p>
		<?php
		break;

	case 'facilitator-add.php':
		?>
		<h3>Add Facilitator</h3>
		<p>
			To add a new facilitator, enter their email address, and their first and
			last names. Email address will be the same email they'll login with, and
			if they're also registered as a professor, should match the email in their
			professor record.
		</p>
		<p>
			If the email address is not currently assigned to a registered user, a new
			user account will be created. Otherwise the facilitator record will be
			paired with the pre-existing user.
		</p>
		<?php
		break;

	case 'facilitator-edit.php':
		?>
		<h3>Edit Facilitator</h3>
		<p>
			Here, you can change the name of a registered facilitator, or activate or
			deactivate their record.
		</p>
		<p>
			If a facilitator is deactivated, they will no longer show up in search.
			Only deactivate a facilitator if they are no longer available to
			facilitate classes.
		</p>
		<p>
			If you wish to reactivate a facilitator, just select the 'Active'
			checkbox, and click 'Update.'
		</p>
		<?php
		break;

	case 'facilitator-inactive.php':
		?>
		<h3>Inactive Facilitators</h3>
		<p>
			Here, all inactive facilitators are listed.
		</p>
		<p>
			If you wish to reactivate a deactivated facilitator, click on 'Edit
			Record' and select the 'Active' checkbox before clicking 'Update.'
		</p>
		<?php
		break;

	case 'facilitator-list.php':
		?>
		<h3>List Facilitators</h3>
		<p>
			All facilitators that are currently active are listed here.
		</p>
		<p>
			You can search through the list by typing a term in the search box. This
			will search by first or last name, displaying the first
			<?php echo MAX_SEARCH_RESULT; ?> results.
		</p>
		<p>
			You can also scroll through, page by page,
			<?php echo MAX_RESULTS_PER_PAGE; ?> results at a time.
		</p>
		<?php
		break;

	case 'facilitator-schedule.php':
		?>
		<h3>Facilitator Schedule</h3>
		<p>
			<?php if (ROLE_ADMIN == get_logged_in_role() ||
								ROLE_DATA_ENTRY == get_logged_in_role()) {?>
				Here, you will see a facilitator's entire schedule, showing the classes
				they're assigned to facilitate, and the students they're assigned to.
				Any classes they teach may also be displayed here, if applicable.
				<?php if (ROLE_ADMIN == get_logged_in_role()) {?>
					</p>
					<p>
						If you wish to alter a facilitator's class assignments, click the
						'Edit' link in the appropriate timetable block.
				<?php }?>
			<?php } else if (ROLE_FACILITATOR == get_logged_in_role()) {?>
				Here you can see your entire schedule, containing classes you teach,
				classes where you're assigned to facilitate, and the students you're
				assigned to.
			</p>
			<p>
				If you find any conflict, issues, or problems, contact
				<a href="mailto:<?php echo ADMIN_CONTACT;?>"><?php echo ADMIN_NAME;?></a>
				as soon as possible.
			<?php }?>
		</p>
		<p>
			Each timetable block contains, in order:
			<ul>
				<li>Course Code</li>
				<li>CRN</li>
				<li>Campus Name</li>
				<li>Room Number</li>
				<li>Professor's Name</li>
				<li>Students assigned (if applicable)</li>
			</ul>
		</p>
		<?php
		break;

	case 'index.php':
		?>
		<h3>Welcome to the CICE Scheduler</h3>
		<p>
			Please login to access the various features of the CICE Scheduler.
		</p>
		<?php
		break;

	case 'login.php':
		?>
		<h3>Login</h3>
		<p>
			Enter your email address, and password to access the CICE Scheduler. If
			you have forgotten your password, or are having any kind of trouble
			logging in, please contact
			<a href="mailto:<?php echo ADMIN_CONTACT;?>"><?php echo ADMIN_NAME;?></a>
			and ask to have your password reset.
		</p>
		<?php
		break;

	case 'professor-add.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'professor-edit.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'professor-inactive.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'professor-list.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'room-add.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'room-list.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-build.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-class.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-list.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'schedule-students.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-add.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-edit.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-inactive.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-list.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-register.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-schedule-add.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-schedule-delete.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-schedule.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'student-search.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-add.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-change-email.php':
		?>
		<h3>Change Email</h3>
		<p>
			Enter your new email address to change it.
		</p>
		<p>
			If you are a facilitator who also teaches classes, please be aware that
			changing your email could cause serious problems, as the records of any
			classes you teach could become disconnected from your account. This could
			result in major schedule conflicts. Please be sure to alert
			<a href="mailto:<?php echo ADMIN_CONTACT;?>"><?php echo ADMIN_NAME;?></a>
			to the change, to prevent any sort of conflicts or errors.
		</p>
		<?php
		break;

	case 'user-change-password.php':
		?>
		<h3>Change Password</h3>
		<p>
			<?php if (require_password_change()) {?>
				You are required to change your password to use the CICE Scheduler.
				Please enter in a new password, and confirm it.
			<?php } else {?>
				To change your password, please enter your old password, your new
				password, and confirm your new password.
			<?php }?>
		</p>
		<p>
			Your new password must be a minimum of <?php echo MIN_PASSWORD_LENGTH;?>
			characters long.
		</p>
		<?php
		break;

	case 'user-list.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-password-reset.php': //TODO: Write incomplete help file
		?>
		<h3></h3>
		<p>

		</p>
		<?php
		break;

	case 'user-role.php': //TODO: Write incomplete help file
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
			You attempted to access a page you are not authorized to access. If you
			think you should be able to access this page, you may not be using the
			right user account. To fix this, log out and log back in. If you're still
			experiencing problems, contact server administration.
		</p>
		<?php
		break;

	case '403.php':
		?>
		<h3>403 Error</h3>
		<p>
			You attempted to access a resource you are not authorized to access,
			likely a directory listing. If you think this is in error, contact server
			administration.
		</p>
		<?php
		break;

	case '404.php':
		?>
		<h3>404 Error</h3>
		<p>
			File not found. You might have typed in a non-existant URL, or clicked a
			broken link.
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
