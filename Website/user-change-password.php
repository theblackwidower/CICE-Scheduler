<?php
include_once "modules/constants.php";
$title = "Change Password";
$restrictionCode = ALL_USERS;
include "modules/header.php";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$old_password = trim($_POST["old_password"]);
	$new_password = trim($_POST["new_password"]);
	$confirm_password = trim($_POST["confirm_password"]);
	$email = get_logged_in_email();

	if (get_logged_in_role() != ROLE_NEW_PASSWORD && $old_password == "")
		$result = "Please enter your current password.";
	else if (get_logged_in_role() != ROLE_NEW_PASSWORD && !login($email, $old_password))
		$result = "Your current password is invalid.";
	else if ($new_password == "")
		$result = "Please enter a new password.";
	else if (strlen($new_password) < MIN_PASSWORD_LENGTH)
		$result = "Password must have at least ".MIN_PASSWORD_LENGTH." characters.";
	else if (strcmp($new_password, $confirm_password) !== 0)
		$result = "New passwords must match.";
	else
	{
		$code = set_password($email, $new_password);
		if ($code === true)
		{
			if (get_logged_in_role() == ROLE_NEW_PASSWORD)
			{
				change_user_role($email, ROLE_FACILITATOR);
				if (login($email, $new_password))
				{
					set_session_message("Password successfully updated.");
					redirect("facilitator-schedule.php");
				}
				else
				{
					set_session_message("Password update failed.");
					redirect("index.php");
				}
			}
			else
			{
				session_unset();
				session_destroy();
				session_start();
				set_session_message("Password successfully updated. Please login with new password.");
				redirect("login.php");
			}
		}
		else
			$result = "Unknown error occured: ".$code;
	}

	echo "<h2>".$result."</h2>";
}
	form_open_post(); ?>
		<?php
		if (get_logged_in_role() != ROLE_NEW_PASSWORD)
		{
		echo '<ul>';
			form_password_box('old_password', 'Current Password');
		echo '</ul>';
		}
		?>
		<ul>
			<?php form_password_box('new_password', 'New Password'); ?>
		</ul>
		<ul>
			<?php form_password_box('confirm_password', 'Confirm Password'); ?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
