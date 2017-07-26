<?php
//header('Content-Type: application/json');
//Security: check token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$headers = apache_request_headers();
if (!isset($headers['Csrftoken']) || $headers['Csrftoken'] !== $_SESSION['csrf_token']) {
  exit(json_encode(['error' => 'Token error.']));
}
//Security: check referrer
if(stripos( $_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'] ) === false || !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
  exit(json_encode(['error' => 'Questionable source. Dieing.']));
}

if(is_callable($_POST['action'] . '_ajax')) {
  call_user_func($_POST['action'] . '_ajax');
}

function update_clients_ajax(){
  $data = $_POST['load'];
  require_once('../app/core.php');
  $response = $BrandDB->update_brands($data);
  exit(json_encode(['load' => $response]));
}
?>
