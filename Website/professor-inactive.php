<?php
include_once "modules/constants.php";
$title = "Inactive Professors";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('inactive-professors');
include "modules/footer.php";
