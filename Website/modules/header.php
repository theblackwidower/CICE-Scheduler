<?php require_once "init.php"; ?>
<!--
CICE Scheduler,
Copyright (C) 2016: T Duke Perry (http://noprestige.com)
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title><?php echo $title; ?></title>
		<!--link rel="icon" type="image/png" href="images/icon.png" /-->
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_FOLDER;?>styles/main.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_FOLDER;?>styles/ajax.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_FOLDER;?>styles/forms.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_FOLDER;?>styles/lists.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_FOLDER;?>styles/schedule.css" />
		<script type="text/javascript" src="<?php echo SITE_FOLDER;?>scripts/main.js"></script>
		<script type="text/javascript" src="<?php echo SITE_FOLDER;?>scripts/ajax-search.js"></script>
		<script type="text/javascript" src="<?php echo SITE_FOLDER;?>scripts/ajax-popup.js"></script>
		<script type="text/javascript" src="<?php echo SITE_FOLDER;?>scripts/ajax-login.js"></script>
		<script type="text/javascript" src="<?php echo SITE_FOLDER;?>scripts/ajax-autocomplete.js"></script>
		<?php
		if ($restrictionCode != PUBLIC_ACCESS)
			echo '<script type="text/javascript"><!--
			window.setInterval(check_login, LOGIN_CHECK_INTERVAL);
			//--></script>';
		?>
	</head>
	<body>
		<div id="header">CICE Scheduler</div>
		<div id="main">
			<div id="sidebar">
				<?php

				$sidebar = array('Main Menu' => 'index.php');
				if (!is_logged_in())
					$sidebar['Login'] = 'login.php';
				else
				{
					$sidebar['Logout'] = 'logout.php';
					$sidebar['Account'] = array(
							'Change Email' => 'user-change-email.php',
							'Change Password' => 'user-change-password.php');

					if (get_logged_in_role() == ROLE_FACILITATOR)
					{
						$sidebar['View Schedule'] = 'facilitator-schedule.php';
					}
					else if (get_logged_in_role() == ROLE_ADMIN || get_logged_in_role() == ROLE_DATA_ENTRY)
					{
						$sidebar['Facilitators'] = array(
							'List Facilitators' => 'facilitator-list.php',
							'Add Facilitator' => 'facilitator-add.php',
							'Inactive Facilitators' => 'facilitator-inactive.php',
							'facilitator-edit.php', 'facilitator-schedule.php');
						$sidebar['Student Schedules'] = array(
							'Course Data' => array(
								'Add New Course' => 'course-add.php',
								'List Courses' => 'course-list.php',
								'Add Class' => 'class-add.php',
								'Inactive Courses' => 'course-inactive.php',
								'class-list.php', 'class-edit.php', 'class-schedule.php',
								'class-time-add.php', 'class-time-delete.php', 'course-edit.php'),
							'Professor and Room Data' => array(
								'Add New Professor' => 'professor-add.php',
								'List Professors' => 'professor-list.php',
								'Inactive Professors' => 'professor-inactive.php',
								'Add Room' => 'room-add.php',
								'List Rooms' => 'room-list.php',
								'professor-edit.php'),
							'Student Data' => array(
								'Add New Student' => 'student-add.php',
								'List Student Schedules' => 'student-list.php',
								'Register Existing Student' => 'student-register.php',
								'Search All Students' => 'student-search.php',
								'Inactive Students' => 'student-inactive.php',
								'student-edit.php', 'student-schedule.php',
								'student-schedule-add.php', 'student-schedule-delete.php'));
						if (get_logged_in_role() == ROLE_ADMIN)
						{
							$sidebar['Facilitator Schedules'] = array(
								'Build Schedules' => 'schedule-build.php',
								'List Schedules' => 'schedule-list.php',
								'schedule-class.php', 'schedule-students.php');
							$sidebar['User Management'] = array(
								'List User Accounts' => 'user-list.php',
								'Add New User Account' => 'user-add.php',
								'user-role.php', 'user-password-reset.php');
						}
						$sidebar['Administration'] = 'administration.php';
					}
				}
				echo build_sidebar($sidebar, $this_file)['output'];
				?>
			</div>
			<div id="content">
				<h1 id="title"><?php echo $title; ?></h1>
				<h2 id="message"><?php print_session_message(); ?></h2>
