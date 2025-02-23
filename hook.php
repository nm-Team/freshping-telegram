<?php

// Get data from webhook
$json_data = file_get_contents("php://input");
if (empty($json_data))
    die();

// Log webhook action
$file = 'access.log';
$current = file_get_contents($file);
$current .= date('[j/M/Y H:i:s]'). " $json_data \n";
file_put_contents($file, $current);

// Get config variables
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

$telegram = new Longman\TelegramBot\Telegram(BOT_TOKEN, BOT_USERNAME);
use Longman\TelegramBot\Request;

$data = json_decode($json_data);
$check_name = $data->check_name;
$check_state = $data->response_summary." (".$data->response_state.")";
$request_url = $data->check_url;
$request_start_time = date(
    'Y-m-d H:i:s T', 
    strtotime( $data->request_datetime )
);
$http_status_code = $data->response_status_code;

$message = "nmTeam System Status Change \n\n$check_name System Status Changed to $check_state \n\n";
$message .= "URL tested: $request_url \n";
$message .= "Test time:  $request_start_time \n";
$message .= "HTTP code:  $http_status_code \n";
$message .= "\n🏷 #SLA\n🔗 https://nmteam.xyz\n👥 @nmteamchat";

$data = [
    'chat_id' => CHAT_ID,
    'text'    => $message,
];

if ( ! in_array( $check_name, $exceptionList ) ) {
    if ( ANY_STATE ) {
        $resutl = Request::sendMessage($data);
    } elseif ( $check_state == 'Not Responding' ) {
        $resutl = Request::sendMessage($data);
    }
}


?>
