# CICE Scheduler

[![Code Climate](https://codeclimate.com/github/theblackwidower/CICE-Scheduler/badges/gpa.svg)](https://codeclimate.com/github/theblackwidower/CICE-Scheduler)

This application is designed to store information on students, professors, and classes; all for the purpose of scheduling learning facilitators to assist the students during their classes.

## Licence

This application was built for, and is exclusively licensed to the Community Integration through Cooperative Education (CICE) department at Durham College. All other use is unauthorized.

If you wish to use this application, in whole or in part, please contact by [email](mailto:theblackwidower@noprestige.com) for permission.

## Installation

This application is to be run on an Apache Server, running PHP, with a PostgreSQL back-end.

To install, create a main user account and database in PostgreSQL, as well as a separate user account called 'apache' for the database backup function.

You can adapt this SQL script to get started.

```
CREATE ROLE cice LOGIN
	UNENCRYPTED PASSWORD 'password';
CREATE DATABASE "CICE_Scheduler"
	WITH OWNER = cice;

CREATE ROLE apache LOGIN;
```

Run all eight SQL scripts in the SQL folder, one at a time.

Add the contents of the Website folder to the Apache server's web folder. It can be installed in either the root, or in a subfolder.

Open modules/constants.php and change the SITE_FOLDER constant to match whatever subfolder the application is stored in on the server. If it's stored in the server root, use nothing more than a forward slash. In this file, you can also enable the email function by changing EMAIL_ENABLED to true. This will allow the system to automatically send new users their account passwords.

Open modules/dblogin.php and edit the data within to match the PostgreSQL database settings, and user information.

Open the application on your web browser and click on "Login". The default admin username and password is 'admin' and 'password'. Once logged in, you will be immediately asked to change your password. This is required. Change the password to something memorable and unique.

After this, the application has been successfully installed.
