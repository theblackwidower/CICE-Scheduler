<?php
include_once "modules/constants.php";
$title = "Add Facilitator";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = trim($_POST["email"]);
	$first_name = trim($_POST["first_name"]);
	$last_name = trim($_POST["last_name"]);

	$result = "";

	if ($email == "")
		$result .= "Please enter an email address.<br />";
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		$result .= '<em>"'.$email.'"</em> is not a valid email address.<br />';
	else
		$email = strtolower($email);

	if ($first_name == "")
		$result .= "Please enter a first name.<br />";

	if ($last_name == "")
		$result .= "Please enter a last name.<br />";

	if ($result == "")
	{
		$is_user = false;
		if (user_exists($email))
		{
			$result .= "User account is already registered.";
			$is_user = true;
		}
		else
		{
			$password = default_password($first_name, $last_name);
			$code = add_user_account($email, $password, ROLE_FACILITATOR, 'true');
			if ($code === true)
			{
				$result .= "User account created with temporary password";
				$is_user = true;
				if (email_password($email, $password))
					$result .= ".<br />Password has been emailed to facilitator.";
				else
					$result .= ": <em>".$password."</em>";
			}
			else
				$result .= "Unknown error occured while creating user account: ".$code;
		}

		if ($is_user)
		{
			if (facilitator_exists($email))
				$result .= "<br />Email is already registered to facilitator.";
			else
			{
				$code = add_facilitator($email, $first_name, $last_name);
				if ($code === true)
				{
					$result .= "<br />Facilitator <em>".format_name($first_name, $last_name, NAME_FORMAT_FIRST_NAME_FIRST)."</em> successfully added.";

					$email = "";
					$first_name = "";
					$last_name = "";
				}
				else
					$result .= "<br />Unknown error occured: ".$code;
			}
		}
	}

	echo "<h2>".$result."</h2>";
}
else
{
	$email = '';
	$first_name = '';
	$last_name = '';
}
	form_open_post(); ?>
		<ul>
			<?php
			form_text_box('email', 'Email', $email);
			?>
		</ul>
		<ul>
			<?php
			form_text_box('first_name', 'First Name', $first_name);
			form_text_box('last_name', 'Last Name', $last_name);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_CREATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
