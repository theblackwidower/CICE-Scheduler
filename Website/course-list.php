<?php
include_once "modules/constants.php";
$title = "List Current Courses";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('courses');
include "modules/footer.php";
