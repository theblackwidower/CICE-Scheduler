<?php
include_once "modules/constants.php";
$title = "List Student Schedules";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('registered-students');
include "modules/footer.php";
