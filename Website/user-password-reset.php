<?php
include_once "modules/constants.php";
$title = "Reset User Password";
$restrictionCode = ROLE_ADMIN;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = $_POST["email"];
	$role_id = $_POST["role_id"];

	$facilitator = get_facilitator($email);
	if ($facilitator !== false)
		$new_password = default_password($facilitator['first_name'], $facilitator['last_name']);
	else
		$new_password = random_password();


	$code = set_password($email, $new_password);
	if ($code === true)
	{
		$result .= "User <em>".$email."</em> account reset with temporary password";
		if (EMAIL_ENABLED && email_password($email, $new_password))
			$result = ".<br />Password has been emailed to user.";
		else
			$result .= ": <em>".$new_password."</em>";
		set_password_change_force($email, 'true');
		set_session_message($result);
		redirect("user-list.php");
	}
	else
		$result .= "Unknown error occured: ".$code;
}
else
{
	if (isset($_GET['email']))
	{
		$email = $_GET['email'];
		if ($email != get_logged_in_email())
		{
			$role_id = get_user_role($email);
			if ($role_id === false)
			{
				set_session_message("User does not exist.");
				redirect('user-list.php');
			}
		}
		else
		{
			set_session_message("This is your account.");
			set_session_message("It's not a good idea to reset your own password.");
			set_session_message("You could permanently lock yourself out.");
			redirect('user-list.php');
		}
	}
	else
	{
		set_session_message("Please select a user.");
		redirect('user-list.php');
	}
}
?>
	<h3>
		Are you sure you want to reset this user's password?<br />
		Their password will be changed to a random sequence,<br />
		and they will be forced to change it at next login.
	</h3>
	<?php form_open_post(); ?>
		<ul>
			<?php
			form_read_only('email', 'Email', $email);
			form_read_only('role_id', 'Role', $role_id);
			?>
		</ul>
		<ul>
			<?php form_question_buttons('user-list.php', array()); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
