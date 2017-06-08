<?php
  require_once './vendor/autoload.php';

  DB::$user = 'gb_dashboard';
  DB::$password = 'globalb2017?dashboard';
  DB::$dbName = 'gb_dashboard';

  require_once('connectors/google_analytics.php');
  require_once('components/analytics.php');
  $analytics = new Google_Data(true);
?>
