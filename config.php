<?php
	$hostname = "localhost";
	$username = "root";
	$password = "";
	$dbname = "ais_mobile";
	$conn = mysqli_connect($hostname, $username, $password, $dbname);
	if (!$conn) {
		die("Koneksi Gagal: " . mysqli_connect_error());
	}
?>