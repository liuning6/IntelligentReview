<?php

class Security {

	public static function mysql_escape_string($string) {

		$hostname = config('database.hostname');
		$database = config('database.database');
		$username = config('database.username');
		$password = config('database.password');

		$con = mysqli_connect($hostname, $username, $password, $database);

		return mysqli_real_escape_string($con, $string);
	}

}