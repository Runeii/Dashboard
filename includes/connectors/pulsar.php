<?php

  use \Curl\Curl;
  $curl = new Curl();
  $curl->setHeader('Content-type', 'application/json');
  $curl->setHeader('Authorization', 'Token token=3d0f426490bcd5a64057665d66dd81ab');
  $curl->get('https://globalbrands.pulsarplatform.com/');
  if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
  } else {
      echo 'Response:' . "\n";
      var_dump($curl->response);
  }

 ?>
