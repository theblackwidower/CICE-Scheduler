<?php
$email = (isset($_COOKIE['email'])?$_COOKIE['email']:"");
require_once '../../modules/constants.php';
require_once '../../modules/functions/forms.php';?>
<h2 class="popup_message">Your session has expired, please log in</h2>
<form onsubmit="ajax_login(this); return false;" onreset="reset_form(this); return false;">
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
