<?php
include_once "modules/constants.php";
$title = "Inactive Facilitators";
$restrictionCode = ROLE_DATA_ENTRY;
include "modules/header.php";
form_ajax_search('inactive-facilitators');
include "modules/footer.php";
