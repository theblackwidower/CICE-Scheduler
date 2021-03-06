# CICE Scheduler

[![Code Climate](https://codeclimate.com/github/theblackwidower/CICE-Scheduler/badges/gpa.svg)](https://codeclimate.com/github/theblackwidower/CICE-Scheduler)

This application is designed to store information on students, professors, and classes; all for the purpose of scheduling learning facilitators to assist the students during their classes.

## Licence

This application was built for, and is exclusively licensed to the Community Integration through Cooperative Education (CICE) department at Durham College. All other use is unauthorized.

If you wish to use this application, in whole or in part, please contact by [email](mailto:theblackwidower@noprestige.com) for permission.

## Integrated Help

This application comes with an integrated help function. For information on the current page, click on the 'Help' button in the bottom left corner of the screen.

Currently, this function is a work in progress, as not all functions have a 'help' page written.

## Installation

This application is to be run on an Apache Server (v2.4 or later), running PHP (v5.6 or later), with a PostgreSQL (v9.4 or later) back-end. PHP should also be setup to access an SMTP server for the automated password email function; but this is not mandatory.

### PostgreSQL setup

To install, create a main user account and database in PostgreSQL.

You can adapt this SQL script to get started. Be sure to change the password.

```
CREATE ROLE cice LOGIN
	ENCRYPTED PASSWORD 'password';
CREATE DATABASE "CICE_Scheduler";
```

Run all eight SQL scripts in the SQL folder, in the numbered order, within the newly-created database. Then, grant all necessary permissions to the main user account using these commands:

```
GRANT SELECT, INSERT ON ALL TABLES IN SCHEMA public TO cice;
GRANT DELETE ON tbl_class_times, tbl_schedule, tbl_assigned_students, tbl_student_classes TO cice;
GRANT UPDATE ON tbl_classes, tbl_courses, tbl_facilitators, tbl_professors, tbl_users, tbl_students TO cice;
REVOKE INSERT ON tbl_days, tbl_role FROM cice;
GRANT USAGE ON ALL SEQUENCES IN SCHEMA public TO cice;
```

As an alternative, you can set the main user account as database owner, and run the setup scripts from the same account. This'll set the main user account as owner of all tables and by doing this, you will grant full access to all aspects of database maintenance to the web interface, including database vacuuming. If you do this, be sure to enable vacuuming in the constants.php file, as explained below.

### Enable backup function

To allow the built-in backup function to run, the webserver needs direct access to PostgreSQL through separate user account called 'apache'. It must also be granted full read access. These SQL commands are all that are needed:

```
CREATE ROLE apache LOGIN;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO apache;
GRANT SELECT ON ALL SEQUENCES IN SCHEMA public TO apache;
```

However the backup function can only run if both Apache and PostgreSQL are running on the same machine. If they are running on separate machines, there is no need to run these commands, because it will not work regardless.

#### Restoring backups

If you wish to restore a backup later, you will need direct access to the server's command line. Start by running the first 7 SQL scripts to clear everything and rebuild the schema. Then run the following command from the bash shell:

```
psql CICE_Scheduler -f cice-scheduler-backup.sql
```

Where 'CICE_Scheduler' is the name of the database, and 'cice-scheduler-backup.sql' is the backup file.

As a final step, run the various permission-setting statements from the PostgreSQL setup stage.

### Apache setup

Add the contents of the Website folder to the Apache server's web folder. It can be installed in either the server root, or in a subfolder.

Open modules/constants.php and change the SITE_FOLDER constant to match whatever subfolder the application is stored in on the server. If it's stored in the server root, use nothing more than a forward slash. You should also change the SITE_URL constant to match the full url of the application, starting with the 'http' and ending with a forward slash.

In this file, you can also enable the email function by changing EMAIL_ENABLED to true. This will allow the system to automatically send new users their account passwords.

Next, change ADMIN_NAME and ADMIN_CONTACT to match the name and email address of the main admin user. Users will be directed to this email address to send messages regarding timetable conflicts, errors, and discrepancies.

If the main database user account is set up as owner of all tables in the database, you can also set ALLOW_VACUUMING to true, allowing database vacuuming to be performed from the Administration page. However, this feature is not mandatory for normal operation.

After this, open up .htaccess. There are eight ErrorDocument statements. Ensure they all point to the correct error message files by editing the subfolders. They should all match whatever was set as the SITE_FOLDER constant, followed by the numerical error code, and a '.php'.

Finally, open modules/dblogin.php and edit the data within to match the PostgreSQL database settings, and user information.

### Final steps

These final steps are for the main admin user.

Open the application on your web browser and click on "Login". The default admin username (email address) and password is 'admin' and 'password'. Once logged in, you will be immediately asked to change your password. This is required. Change the password to something memorable and unique. It is also recommended that you change the 'admin' username by clicking on 'Account'->'Change Email' in the sidebar and changing it to your email address.

Next, you'll want to start by registering the current semester in the database. Click on 'Administration' in the sidebar, and under 'Add New Semester,' type in the current semester code. It should start with an 'F' or 'W' for either fall or winter, followed by the current year. Be sure to set the correct start and end dates, and click 'Add'.

After this, the application has been successfully installed.
