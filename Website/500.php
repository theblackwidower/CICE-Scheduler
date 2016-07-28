<?php
include_once "modules/constants.php";
$title = "ERROR 500: Internal Server Error";
$restrictionCode = PUBLIC_ACCESS;
include "modules/header.php";?>
<p>
	Something went wrong on our end. Please alert server administration.<br />
	The time is: [<?php echo date('D M d H:i:s.u Y'); /*[Sat Apr 02 06:32:22.304463 2016]*/ ?>]
</p>
<?php include "modules/footer.php";
