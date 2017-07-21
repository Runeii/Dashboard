<?php
  define( 'DASHROOT', dirname(dirname(__FILE__), 2) . '/' );
  define( 'NOTICESLOG', DASHROOT . '/logs/notices_' . date('dmy') );
  define( 'ERRORLOG', DASHROOT . '/logs/error_' . date('dmy') );

  function customlog($message, $type = 'error', $append = true) {
    if($type === 'error') {
      $file = ERRORLOG;
    } else {
      $file = NOTICESLOG;
    }
    if($append === true) {
      file_put_contents($file, $message, 0, $flag, FILE_APPEND);
    } else {
      file_put_contents($file, $message, 0, $flag);
    }
  }

  require_once DASHROOT . '/vendor/autoload.php';

  DB::$user = 'gb_dashboard';
  DB::$password = 'globalb2017?dashboard';
  DB::$dbName = 'gb_dashboard';

  require_once DASHROOT .'/includes/connectors/db.php';
  $database = new DBAccess();

  require_once DASHROOT .'/includes/connectors/brands.php';
  $BrandDB = new Brands();

  require_once DASHROOT .'/includes/components/analytics.php';
  $analytics = new Google_Data();

  //Facebook
  require_once DASHROOT .'/includes/components/facebook.php';
  $facebook = new Facebook();

  //Twitter
  require_once DASHROOT .'/includes/components/twitter.php';
  $twitter = new Twitter();

  //Instagram
  require_once DASHROOT .'/includes/components/instagram.php';
  $instagram = new Instagram();
?>
