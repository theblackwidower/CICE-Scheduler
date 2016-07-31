<?php
include_once "modules/constants.php";
$title = "Welcome";
$restrictionCode = PUBLIC_ACCESS;
include "modules/header.php";?>
<p>
	Welcome to the Durham College CICE Department Scheduler.
	Please login using the link at the left to access your latest schedule.
</p>
<p>
	If you notice any problems with your schedule, please alert
	<a href="mailto:<?php echo ADMIN_CONTACT;?>"><?php echo ADMIN_NAME;?></a>
	as soon as possible.
</p>
<?php include "modules/footer.php";
