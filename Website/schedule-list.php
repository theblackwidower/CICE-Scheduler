<?php
include_once "modules/constants.php";
$title = "List Scheduled Blocks";
$restrictionCode = ROLE_ADMIN;
include "modules/header.php";
$semester_id = get_default_semester();
$all_classes = all_scheduled_classes($semester_id);
?>
	<h2>Classes with Assigned Facilitators</h2>
	<div class="search_results">
		<?php display_search_results('scheduling', $all_classes); ?>
	</div>
<?php include "modules/footer.php";
