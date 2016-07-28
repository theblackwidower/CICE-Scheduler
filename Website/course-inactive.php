<?php
include_once "modules/constants.php";
$title = "Inactive Courses";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('inactive-courses');
include "modules/footer.php";
