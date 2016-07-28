<?php
include_once "modules/constants.php";
$title = "Inactive Students";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('inactive-students');
include "modules/footer.php";
