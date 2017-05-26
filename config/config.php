<?php
require_once('dbconn.php');

//Establish config connection
$db_conn = mysqli_connect($db_servername, $db_username, $db_password);

mysqli_select_db($db_conn, $db_name);

if (mysqli_connect_errno($db_conn)) {
    die("Failed to connect to MySQL: " . mysqli_connect_error($db_conn));
}

//Get Sub-folder name
if (basename(dirname($_SERVER['PHP_SELF']))) {
    $subDirectory = "/".basename(dirname($_SERVER['PHP_SELF']));
} else {
    $subDirectory = "";
}


$IPrange = "555.555.";
$customerLinkStr = "https://intranet.tlcdelivers.com/TLCWebLSN/customer.asp?Cust_ID=";
$buildURLStr = "";
