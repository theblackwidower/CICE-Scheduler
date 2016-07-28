<?php
include_once "modules/constants.php";
$title = "Add Professor";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$professor_id = $_POST['professor_id'];
	$first_name = trim($_POST["first_name"]);
	$last_name = trim($_POST["last_name"]);
	$email = trim($_POST["email"]);
	$is_active = isset($_POST["is_active"])?'true':'false';

	$result = "";

	if ($first_name == "")
		$result .= "Please enter a first name.<br />";

	if ($last_name == "")
		$result .= "Please enter a last name.<br />";

	if ($email == "")
		$email = null;
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		$result .= '<em>"'.$email.'"</em> is not a valid email address.<br />';
	else if (professor_email_exists($email))
		$result .= '<em>"'.$email.'"</em> is already registered to a professor.<br />';

	if ($result == "")
	{
		$code = update_professor($professor_id, $first_name, $last_name, $email, $is_active);
		if ($code === true)
		{
			set_session_message("Professor <em>".format_name($first_name, $last_name, NAME_FORMAT_LAST_NAME_FIRST)."</em> successfully updated.");
			redirect("professor-list.php");
		}
		else
			$result = "Unknown error occured: ".$code;
	}

	echo "<h2>".$result."</h2>";
}
else
{
	if (isset($_GET['id']))
	{
		$professor_id = $_GET['id'];
		$professor = get_professor($professor_id);
		if ($professor === false)
		{
			set_session_message("Professor #<em>".$professor_id."</em> is not registered in the system.");
			redirect("professor-list.php");
		}
		else
		{
			$first_name = $professor['first_name'];
			$last_name = $professor['last_name'];
			$email = $professor['email'];
			$is_active = $professor['is_active']?'true':'false';
		}
	}
	else
	{
		set_session_message("Please select a professor.");
		redirect("professor-list.php");
	}
}
	form_open_post(); ?>
		<ul>
			<input name="professor_id" type="hidden" value="<?php echo $professor_id; ?>" />
			<?php
			form_text_box('first_name', 'First Name', $first_name);
			form_text_box('last_name', 'Last Name', $last_name);
			?>
		</ul>
		<ul>
			<?php
			form_text_box('email', 'Email Address (optional)', $email);
			form_checkbox('is_active', 'Active', $is_active);
			?>
		</ul>
		<ul>
			<?php form_submit_buttons(BTN_TYPE_UPDATE); ?>
		</ul>
	</form>
<?php include "modules/footer.php";
