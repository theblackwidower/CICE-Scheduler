<?php
include_once "modules/constants.php";
$title = "Change Email";
$restrictionCode = ALL_USERS;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$old_email = get_logged_in_email();
	$new_email = trim($_POST["new_email"]);

	$result = "";

	if ($old_email != trim($_POST["old_email"]))
		$result .= 'Error of inconsistancy found. You tried to change the email for <em>"'.
				trim($_POST["old_email"]).'"</em> but are logged in as <em>"'.
				$old_email.'"</em>. You might want to try again.<br />';
	else if ($old_email == "" || !user_exists($old_email))
		$result .= "User record cannot be found.<br />";

	if ($new_email == "")
		$result .= "Please enter a new email address.<br />";
	else if (!filter_var($new_email, FILTER_VALIDATE_EMAIL))
		$result .= '<em>"'.$new_email.'"</em> is not a valid email address.<br />';
	else if (user_exists($new_email))
		$result .= '<em>"'.$new_email.'"</em> is already registered to another user.<br />';
	else
		$email = strtolower($email);

	if ($result == "")
	{
		$code = change_email($old_email, $new_email);
		if ($code === true)
		{
			session_unset();
			session_destroy();
			session_start();
			set_session_message("Email successfully updated to <em>".$new_email."</em>. Please login with new email.");
			header("Location: login.php");
		}
		else
			$result = "Unknown error occured: ".$code;
	}

	echo "<h2>".$result."</h2>";
}
else
{
	$old_email = get_logged_in_email();
	if (!user_exists($old_email))
	{
		set_session_message("Your account <em>".$old_email."</em> is not registered in the database.");
		redirect("index.php");
	}
	else
		$new_email = '';
}
	form_open_post(); ?>
		<ul>
			<?php form_read_only('old_email', 'Old Email', $old_email); ?>
		</ul>
		<ul>
			<?php form_text_box('new_email', 'New Email', $new_email); ?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
