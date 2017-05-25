<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    //DB connection string and Global variables
    include_once('../config/config.php');

    //Admin panel functions
    include_once('../core/functions.php');
    ?>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>YouSeeMore - Utilities</title>

    <!-- Core CSS Libraries -->
    <link rel="stylesheet" type="text/css" href="//<?php echo $_SERVER['HTTP_HOST']; ?>/css/admin.min.css">

    <!-- Admin Panel Fonts -->
    <link rel="stylesheet" type="text/css" href="//<?php echo $_SERVER['HTTP_HOST'] ?>/css/font-awesome.min.css">

    <!-- Admin Panel CSS -->
    <link rel="stylesheet" type="text/css" href="//<?php echo $_SERVER['HTTP_HOST']; ?>/css/sb-admin.min.css">

    <!-- Custom Fonts from: localfont.com -->
    <link href='//<?php echo $_SERVER['HTTP_HOST'] ?>/css/fonts.min.css' rel='stylesheet'>

    <!-- Core JS Libraries -->
    <script type="text/javascript" language="javascript" src="//<?php echo $_SERVER['HTTP_HOST']; ?>/js/admin.min.js"></script>

    <!-- Custom Functions -->
    <script type="text/javascript" language="javascript" src="//<?php echo $_SERVER['HTTP_HOST']; ?>/js/functions.min.js"></script>
    <style>
        body {
            background-color: #fcfcfc;
        }
        #wrapper {
            padding: 0;
            margin: 0;
        }
        .card {
            box-shadow: 0 1px 3px 0 rgba(0,0,0,.15);
            border-radius: 2px;
            border-top: 1px solid transparent;
            border-bottom: 1px solid transparent;
            background-color: #fff;
            margin: 30px 0;
        }
        .card>:last-child {
            margin-bottom: 30px;
        }
        .card .card-body {
            padding: 0 30px;
        }
        #webslideDialog iframe {
            width: 100%;
            height: 400px;
            border: none;
        }

        .modal-dialog {
            width: 50%;
        }
        footer {
            display: inline-block;
            visibility: visible;
        }
    </style>
    <?php
    if ($_GET['modal'] == 'true'){
        ?>
        <style>
            html, body {
                background-color: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            #page-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                min-height: 0 !important;
            }
            .navbar-inverse, .breadcrumb, footer {
                display: none !important;
                visibility: hidden !important;
            }
            .card {
                margin: 0 !important;
            }
        </style>
        <?php
    }
    ?>
</head>
<body>

<div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="admin-topnav">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">YOUSEEMORE</a>
        </div>
    </nav>

    <div id='page-wrapper'>
        <div class='container-fluid'>