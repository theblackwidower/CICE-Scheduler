<?php
include_once "modules/constants.php";
$title = "Student Search";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('students');
include "modules/footer.php";
