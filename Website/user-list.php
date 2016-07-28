<?php
include_once "modules/constants.php";
$title = "List User Accounts";
$restrictionCode = ROLE_ADMIN;
include "modules/header.php";
form_ajax_search('users');
include "modules/footer.php";
