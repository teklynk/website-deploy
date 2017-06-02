<?php
require_once('dbconn.php');

//Establish config connection
$db_conn = mysqli_connect($db_servername, $db_username, $db_password);

mysqli_select_db($db_conn, $db_name);

if (mysqli_connect_errno($db_conn)) {
    die("Failed to connect to MySQL: " . mysqli_connect_error($db_conn));
}

//Global Variables
$IPrange = "";
$ysmArchiveDir = "/var/www/archive";
$ysmSitesDir = "/var/www/html";
$customerLinkStr = "https://intranet.tlcdelivers.com/TLCWebLSN/customer.asp?Cust_ID=";
$ysmServer = "http://10.10.15.142";
$buildServer = "https://jenkins.tlcdelivers.com/buildByToken/buildWithParameters?job=YSM7_Demo_Parameterized_Add_Site&token=deployYSMCustomerWebApp";

//Get Sub-folder name
$subURL = $_SERVER['REQUEST_URI'];
$subPath = parse_url($subURL, PHP_URL_PATH);
$subDir = explode('/', $subPath)[1];
$subDir = trim($subDir);

if (strpos($subDir, 'admin') !== false || strpos($subDir, '.php') !== false ) {
    $subDirectory = '';
} else {
    $subDirectory = '/'.$subDir;
}