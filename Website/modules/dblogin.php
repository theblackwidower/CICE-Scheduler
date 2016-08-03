<?php
define('DATABASE_NAME', 'CICE_Scheduler');
/*
connect:
Returns a new PDO data connection.
All connection information can be changed by editing the variables within.
The database name is stored in the constant above.
NB: If the database server is not on localhost (127.0.0.1),
the built-in database backup function will not work.
*/
function connect()
{
	$host = '127.0.0.1';
	$port = '5432';
	$user = 'cice';
	$password = 'password';

	return new PDO("pgsql:host=".$host.";port=".$port.";dbname=".DATABASE_NAME, $user, $password);
}
