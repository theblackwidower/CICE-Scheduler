<?php
include_once "modules/constants.php";
$title = "Administration Settings";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

define("DEFAULT_SEMESTER", 'default_semester');
define("ADD_SEMESTER", 'add_semester');
define("ADD_CAMPUS", 'add_campus');
define("MAINTENANCE", 'database_maintenance');

	define("STANDARD_VACUUM", 'vacuum');
	define("FULL_VACUUM", 'full_vacuum');

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$section_id = $_GET["section"];
	$message = "";

	if ($section_id == DEFAULT_SEMESTER)
	{
		set_default_semester($_POST['semester']);
		$message = "Semester now set to: ".get_default_semester();
	}
	else if ($section_id == MAINTENANCE)
	{
		if ($_POST['operation'] == FULL_VACUUM)
		{
			$stmt = $conn->prepare('VACUUM FULL');
			$name = "Full vacuum";
		}
		else if ($_POST['operation'] == STANDARD_VACUUM)
		{
			$stmt = $conn->prepare('VACUUM ANALYSE');
			$name = "Vacuum & analyse";
		}

		$timestamp = time();
		$result = $stmt->execute();
		$duration = time() - $timestamp;
		if ($result)
			$message = $name." Completed in ".(($duration < 1)?'<1':$duration)." ".(($duration < 2)?'second':'seconds').'.';
		else
			$message = "Unknown error occurred while vacuuming: ".$stmt->errorCode();
	}
	else if (ROLE_ADMIN == get_logged_in_role())
	{
		if ($section_id == ADD_SEMESTER)
		{
			$semester_id = strtoupper(trim($_POST['semester_id']));
			$start_year = trim($_POST['start_year']);
			$start_month = trim($_POST['start_month']);
			$start_day = trim($_POST['start_day']);
			$end_year = trim($_POST['end_year']);
			$end_month = trim($_POST['end_month']);
			$end_day = trim($_POST['end_day']);

			if ($semester_id == "")
				$message .= "Please enter a semester id.<br />";


			if ($start_year == "")
				$message .= "Please enter a start year.<br />";
			else if (!is_numeric($start_year))
				$message .= "Start year must be a number.<br />";
			else if ($start_year < 2000)
				$message .= "Start year cannot be before 2000.<br />";

			if ($start_month < 1)
				$message .= "Please select a start month.<br />";

			if ($start_day == "")
				$message .= "Please enter a start day.<br />";
			else if (!is_numeric($start_day))
				$message .= "Start day must be a number.<br />";

			if ($end_year == "")
				$message .= "Please enter an end year.<br />";
			else if (!is_numeric($end_year))
				$message .= "End year must be a number.<br />";
			else if ($end_year < 2000)
				$message .= "End year cannot be before 2000.<br />";

			if ($end_month < 1)
				$message .= "Please select an end month.<br />";

			if ($end_day == "")
				$message .= "Please enter an end day.<br />";
			else if (!is_numeric($end_day))
				$message .= "End day must be a number.<br />";

			if ($message == "")
			{
				if (is_registered_semester($semester_id))
					$message = "Semester ID is already taken.";
				else if (!checkdate($start_month, $start_day, $start_year))
					$message = "Start date is invalid.";
				else if (!checkdate($end_month, $end_day, $end_year))
					$message = "End date is invalid.";
				else
				{
					$start_date = $start_year.'-'.str_pad($start_month, 2, '0', STR_PAD_LEFT).'-'.str_pad($start_day, 2, '0', STR_PAD_LEFT);
					$end_date = $end_year.'-'.str_pad($end_month, 2, '0', STR_PAD_LEFT).'-'.str_pad($end_day, 2, '0', STR_PAD_LEFT);
					$code = add_semester($semester_id, $start_date, $end_date);
					if ($code === true)
						$message = "Semester <em>".$semester_id."</em> successfully added.";
					else
						$message = "Unknown error occured: ".$code;
				}
			}
		}
		else if ($section_id == ADD_CAMPUS)
		{
			$campus_id = strtoupper(trim($_POST['campus_id']));
			$campus_name = trim($_POST['campus_name']);

			if ($campus_id == "")
				$message .= "Please enter a campus id.<br />";

			if ($campus_name == "")
				$message .= "Please enter a campus name.<br />";

			if ($message == "")
			{
				if (campus_exists($campus_id))
					$message = "Campus code is already taken.";
				else
				{
					$code = add_campus($campus_id, $campus_name);
					if ($code === true)
						$message = "Campus <em>".$campus_id.' - '.$campus_name."</em> successfully added.";
					else
						$message = "Unknown error occured: ".$code;
				}
			}
		}
	}

	echo "<h2>".$message."</h2>";
}

?>
<div id="admin_panel">
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?section='.DEFAULT_SEMESTER;?>">
		<h3>Set Default Semester:</h3>

		<select name="semester">
			<?php print_ddl(get_all_semesters(), 'semester_id', 'semester_id', get_default_semester()); ?>
		</select>
		<input type="submit" value="Set" />

	</form>
	<?php
	if (ROLE_ADMIN == get_logged_in_role())
	{
		echo '
		<form method="post" action="'.$_SERVER['PHP_SELF'].'?section='.ADD_CAMPUS.'">
			<h3>Add New Campus:</h3>';
			$all_campuses = get_all_campuses();
			foreach ($all_campuses as $campus)
				echo '<div>'.$campus['campus_id'].' - '.$campus['campus_name'].'</div><br />';
			echo '
			<div>
				<label for="campus_id">Campus Code: </label>
				<input name="campus_id" id="campus_id" type="text" value="" size="1" maxlength="1" /><br />

				<label for="campus_name">Campus Name: </label>
				<input name="campus_name" id="campus_name" type="text" value="" size="10" maxlength="10" />
			</div>
			<br />
			<input type="submit" value="Add" />
		</form>
		<form method="post" action="'.$_SERVER['PHP_SELF'].'?section='.ADD_SEMESTER.'">
			<h3>Add New Semester:</h3>
			<div>
				<label for="semester_id">Semester Code: </label>
				<input name="semester_id" id="semester_id" type="text" value="" size="5" maxlength="5" oninput="default_semester(this);" /><br />

				<label for="start_year">Start Date: </label>
				<input name="start_year" id="start_year" type="text" value="" size="4" maxlength="4" />
				<select name="start_month" id="start_month">';
					print_month_ddl();
				echo '
				</select>
				<input name="start_day" type="text" value="" size="2" maxlength="2" /><br />

				<label for="end_year">End Date: </label>
				<input name="end_year" id="end_year" type="text" value="" size="4" maxlength="4" />
				<select name="end_month" id="end_month">';
					print_month_ddl();
				echo '
				</select>
				<input name="end_day" type="text" value="" size="2" maxlength="2" />
			</div>
			<br />
			<input type="submit" value="Add" />
		</form>
		<form method="post" action="'.$_SERVER['PHP_SELF'].'?section='.MAINTENANCE.'">
			<h3>Database Maintenance:</h3>
			<div>
				<!--<a href="backup-database.php?t=full">Download Complete Database Backup</a><br />
				<a href="backup-database.php?t=schema">Download Schema Backup</a><br />-->
				<a href="backup-database.php?t=data">Download Backup File</a>
			</div>
			<br />
			<input id="operation" name="operation" type="hidden" value="" />
			<input type="submit" value="Vacuum" onclick="document.getElementById(\'operation\').value=\''.STANDARD_VACUUM.'\';" />
			<input type="submit" value="Full Vacuum" onclick="document.getElementById(\'operation\').value=\''.FULL_VACUUM.'\';" />
		</form>';
	} ?>
</div>
<?php include "modules/footer.php";
