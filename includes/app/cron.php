<?php
  ini_set('max_execution_time', 0);

  require_once('core.php');

  $analytics->refresh_data();
  $twitter->refresh_data();
  $facebook->refresh_data();
  $instagram->refresh_data();

  if (php_sapi_name() == "cli") {
      // In cli-mode
      file_put_contents(NOTICESLOG, 'CRON: Data updated.');
  } else {
      // Not in cli-mode
      echo 'Update complete.';
  }
?>
