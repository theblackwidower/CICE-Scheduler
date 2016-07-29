<?php
include_once "modules/constants.php";
$title = "Edit User Role";
$restrictionCode = ROLE_ADMIN;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = trim($_POST["email"]);
	$role_id = trim($_POST["role_id"]);

	$code = change_user_role($email, $role_id);
	if ($code === true)
	{
		set_session_message("User role successfully changed to <em>".get_role_name($role_id)."</em>.");
		redirect("user-list.php");
	}
	else
		echo "<h2>Unknown error occured: ".$code."</h2>";
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
				set_session_message("<em>".$email."</em> is not a registered user.");
				redirect("user-list.php");
			}
		}
		else
		{
			set_session_message("This is your account.");
			set_session_message("It's not a good idea to alter your role.");
			set_session_message("You could permanently lock yourself out.");
			redirect('user-list.php');
		}
	}
	else
	{
		set_session_message("Please select a user.");
		redirect("user-list.php");
	}
}
	form_open_post(); ?>
		<ul>
			<?php
			form_read_only('email', 'User Email', $email);
			?>
		</ul>
		<ul>
			<?php
			form_drop_down_box('role_id', 'User Role', $role_id);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
