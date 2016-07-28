<?php
include_once "modules/constants.php";
$title = "ERROR 401: Unauthorized";
$restrictionCode = PUBLIC_ACCESS;
include "modules/header.php";?>
<p>
	Authentication Error. You are not logged in.
</p>
<?php include "modules/footer.php";
