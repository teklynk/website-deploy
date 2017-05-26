<?php
//Get Sub-folder name
if (basename(dirname($_SERVER['PHP_SELF']))) {
    $subDirectory = "/".basename(dirname($_SERVER['PHP_SELF']));
} else {
    $subDirectory = "";
}

$IPrange = "555.555.";
$customerLinkStr = "https://intranet.tlcdelivers.com/TLCWebLSN/customer.asp?Cust_ID=";
$buildURLStr = "";
