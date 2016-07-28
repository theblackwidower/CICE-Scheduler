<?php
include_once "modules/constants.php";
$title = "List Facilitators";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('facilitators');
include "modules/footer.php";
