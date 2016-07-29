<?php
include_once "modules/constants.php";
$title = "Login";
$restrictionCode = PUBLIC_ACCESS;
include "modules/header.php";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	require 'modules/processes/login.php';

	if ($result === 'logged in')
	{
		if (get_logged_in_role() == ROLE_ADMIN || get_logged_in_role() == ROLE_DATA_ENTRY)
			redirect("administration.php");
		else if (get_logged_in_role() == ROLE_FACILITATOR)
			redirect("facilitator-schedule.php");
		else
			redirect("index.php");
	}
	else if ($result === 'change password')
	{
		set_session_message("Your password has been recently reset.<br />Please change it to something more familiar.");
		redirect("user-change-password.php");
	}
	echo "<h2>".$result."</h2>";
}
else
	$email = (isset($_COOKIE['email'])?$_COOKIE['email']:"");

	form_open_post(); ?>
		<ul>
			<?php form_text_box('login_email', 'Email', $email); ?>
		</ul>
		<ul>
			<?php form_password_box('password', 'Password'); ?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_LOGIN); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
