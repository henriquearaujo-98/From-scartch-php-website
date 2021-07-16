<?php

$url = "localhost";
$user = "root";
$password = "";
$db = "mocksite";

$conn = mysqli_connect($url, $user, $password, $db);

if(mysqli_connect_errno()){
	echo "DB error: " . mysqli_connect_error();
}

?>