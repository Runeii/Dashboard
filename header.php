<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html class="no-js" lang="">
<head>
  <?php include('includes/functions.php'); ?>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Global Brands Communications Dashboard</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">
  <link rel="stylesheet" href="assets/css/style-min.css">
  <link rel="stylesheet" href="assets/css/font-awesome.min.css">
  <script src="assets/js/vendor/chart.js"></script>
</head>
<body>
<!--<div id="background"></div>-->
