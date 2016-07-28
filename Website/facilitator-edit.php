<?php
include_once "modules/constants.php";
$title = "Edit Facilitator";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = trim($_POST["email"]);
	$first_name = trim($_POST["first_name"]);
	$last_name = trim($_POST["last_name"]);
	$is_active = isset($_POST["is_active"])?'true':'false';

	$result = "";

	if ($first_name == "")
		$result .= "Please enter a first name.<br />";

	if ($last_name == "")
		$result .= "Please enter a last name.<br />";

	if ($result == "")
	{
		$code = update_facilitator($email, $first_name, $last_name, $is_active);
		if ($code === true)
		{
			set_session_message("Facilitator <em>".$email."</em> successfully updated.");
			redirect("facilitator-list.php");
		}
		else
			$result = "Unknown error occured: ".$code;
	}

	echo "<h2>".$result."</h2>";
}
else
{
	if (isset($_GET['email']))
	{
		$email = $_GET['email'];
		$facilitator = get_facilitator($email);
		if ($facilitator !== false)
		{
			$first_name = $facilitator['first_name'];
			$last_name = $facilitator['last_name'];
			$is_active = $facilitator['is_active']?'true':'false';
		}
		else
		{
			set_session_message("<em>".$email."</em> is not registered as a facilitator.");
			redirect("facilitator-list.php");
		}
	}
	else
	{
		set_session_message("Please select a facilitator.");
		redirect("facilitator-list.php");
	}
}
	form_open_post(); ?>
		<ul>
			<?php
			form_read_only('email', 'Email', $email);
			form_checkbox('is_active', 'Active', $is_active);
			?>
		</ul>
		<ul>
			<?php
			form_text_box('first_name', 'First Name', $first_name);
			form_text_box('last_name', 'Last Name', $last_name);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
