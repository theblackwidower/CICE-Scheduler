<?php
include_once "modules/constants.php";
$title = "Add User Account";
$restrictionCode = ROLE_ADMIN;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = trim($_POST["email"]);
	$role_id = trim($_POST["role_id"]);
	$force_new_password = isset($_POST["force_new_password"])?'true':'false';
	$password = trim($_POST["password"]);
	$confirm_password = trim($_POST["confirm_password"]);
	$random_password = false;

	$result = "";

	if ($email == "")
		$result .= "Please enter an email address.<br />";
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		$result .= '<em>"'.$email.'"</em> is not a valid email address.<br />';

	if (strcmp($password, $confirm_password) !== 0)
		$result .= "New passwords must match.";
	else if ($password == "")
		$random_password = true;
	else if (strlen($password) < MIN_PASSWORD_LENGTH)
		$result .= "Password must have at least ".MIN_PASSWORD_LENGTH." characters.";

	if ($result == "")
	{
		if (user_exists($email))
			$result = "Email is already registered to an account.";
		else
		{
			if ($random_password)
				$password = random_password();

			$code = add_user_account($email, $password, $role_id, $force_new_password);
			if ($code === true)
			{
				$result = "User <em>".$email."</em> successfully created";

				if ($random_password)
				{
					$result .= " with random password";
					if (EMAIL_ENABLED && email_password($email, $new_password))
						$result = ".<br />Password has been emailed to user.";
					else
						$result .= ": <em>".$new_password."</em>";
				}
				else
					$result .= ".";
				$email = "";
			}
			else
				$result = "Unknown error occured: ".$code;
		}
	}

	echo "<h2>".$result."</h2>";
}
else
{
	$email = '';
	$role_id = ROLE_DATA_ENTRY;
	$force_new_password = 'true';
}
	form_open_post(); ?>
		<ul>
			<?php form_text_box('email', 'Email', $email); ?>
		</ul>
		<ul>
			<?php form_drop_down_box('role_id', 'User Role', $role_id); ?>
			<?php form_checkbox('force_new_password', 'Force Password Change', $force_new_password); ?>
		</ul>
		<ul>
			<?php form_password_box('password', 'Password<br />(leave blank for a random password)'); ?>
		</ul>
		<ul>
			<?php form_password_box('confirm_password', 'Confirm Password'); ?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
