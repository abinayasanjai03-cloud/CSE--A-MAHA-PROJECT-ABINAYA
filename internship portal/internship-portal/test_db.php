<?php

$host="db01.dbhost.dev";
$user="user_455jdvbhe";
$pass="p455jdvbhe";
$db="db_455jdvbhe";
$port=5051;

$conn = new mysqli($host,$user,$pass,$db,$port);

if($conn->connect_error){
    die("Connection Failed: ".$conn->connect_error);
}

echo "Database Connected Successfully!";
?>
