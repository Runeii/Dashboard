<?php
  require_once __DIR__ . '/../vendor/autoload.php';

  //Core handles variables etc
  require_once('app/core.php');

  DB::$user = 'gb_dashboard';
  DB::$password = 'globalb2017?dashboard';
  DB::$dbName = 'gb_dashboard';

  require_once('components/analytics.php');
  $analytics = new Google_Data();

  //Facebook
  require_once('components/facebook.php');
  $facebook = new Facebook();

  //Twitter
  require_once('components/twitter.php');
  $twitter = new Twitter();

  //Instagram
  require_once('components/instagram.php');
  $instagram = new Instagram();
?>
