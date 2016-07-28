<?php
include_once "modules/constants.php";
$title = "List Professors";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('professors');
include "modules/footer.php";
