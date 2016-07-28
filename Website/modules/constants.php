<?php
//Linked to email_password function in modules/functions/build.php.
//Be sure that the email message in this function is appropriate.
define("EMAIL_ENABLED", false);

//should reference the subfolder the application is running in on the webserver.
//if it's running on the root of the server, use a forward slash. '/'
define("SITE_FOLDER", '/cice/');

define("START_SCHEDULE", 8); // 24-hour clock, 8am
define("END_SCHEDULE", 18);  // 24-hour clock, 6pm

define("MAX_SEARCH_RESULT", 100);
define("MAX_RESULTS_PER_PAGE", 10);
define("MAX_AUTOCOMPLETE_RESULT", 5);

define("COOKIE_EXPIRY", 2592000);	// 60*60*24*30 --- 30 days

define("ALL_USERS", 'U');
define("PUBLIC_ACCESS", 'P');

define("ROLE_ADMIN", 'A');
define("ROLE_DATA_ENTRY", 'E');
define("ROLE_FACILITATOR", 'F');
define("ROLE_NEW_PASSWORD", 'N');
define("ROLE_DISABLED", 'D');

define("MIN_PASSWORD_LENGTH", 8);
define("MAX_EMAIL_LENGTH", 255);
define("MAX_NAME_FIELD_LENGTH", 30);

define("SCHEDULE_ROLE_FACILITATE", 'F');
define("SCHEDULE_ROLE_TEACH", 'T');
define("SCHEDULE_ROLE_NEW", 'N');

define("NAME_FORMAT_LAST_NAME_FIRST", 1);
define("NAME_FORMAT_FIRST_NAME_FIRST", 2);
define("NAME_FORMAT_FIRST_INITIAL_LAST_NAME", 3);
define("NAME_FORMAT_LAST_NAME_FIRST_INITIAL", 4);
define("NAME_FORMAT_FIRST_NAME_LAST_INITIAL", 5);

define("TT_LINK_NONE", 6);
define("TT_LINK_CLASS_TIME_DELETE", 7);
define("TT_LINK_SCHEDULE_EDIT", 8);
define("TT_LINK_SCHEDULE_EDIT_FROM_STUDENT", 9);

define("BTN_TYPE_CREATE", 'Create');
define("BTN_TYPE_REGISTER", 'Register');
define("BTN_TYPE_UPDATE", 'Update');
define("BTN_TYPE_LOGIN", 'Log In');

//Database rules
//Defined in database constraints as well
//Any changes should be reflected in database constraints, triggers, and functions
define("MAX_HOURS_IN_DAY", 8);
define("MIN_HOURS_TRAVEL_TIME", 2);
define("MAX_HOURS_STRAIGHT", 4);
define("MAX_STUDENTS_PER_FACILITATOR", 3);
define("MAX_CLASS_LENGTH", 4);

define("SQL_NAME_SEARCH", "(LOWER(first_name) LIKE LOWER(:search) OR LOWER(last_name) LIKE LOWER(:search))");
