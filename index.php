<?php

header('Content-Type: text/html; charset=utf-8');

define("TOKEN", '');

$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (!empty($data['message']['text'])) {

    $chat_id = $data['message']['from']['id'];
    $user_name = $data['message']['from']['username'];
    $first_name = $data['message']['from']['first_name'];
    $last_name = $data['message']['from']['last_name'];
    $text = trim($data['message']['text']);
    $text_array = explode(" ", $text);

    if ($text == '/start') {
        $text_return = "Катя привет! Я ни разу не вспомнил видео Игоря Линка двухлетней давности и никто мне не кидал видос из тиктока с похожим ботом. Наслаждайся моим интелектом)";
        message_to_telegram($chat_id, $text_return);
    } else if ($text == '/Еще!') {
        $compliments = file_get_contents("./compliments.txt");
        $compliments = explode("\n", $compliments);

        $compliment = $compliments[rand(0, count($compliments) - 1)];

        $text_return = $compliment;
        sendImage($chat_id, $text_return);
    }
}

function sendit($response, $method = "sendMessage"){

    $ch = curl_init();
    $ch_post = [
        CURLOPT_URL => 'https://api.telegram.org/bot' . TOKEN . "/$method",
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POSTFIELDS => $response
    ];

    curl_setopt_array($ch, $ch_post);
    curl_exec($ch);
}

function message_to_telegram($chat_id, $text){
    $buttons =  json_encode([
        "keyboard" => [
            [["text" => "/Еще!"]]
        ],
        'one_time_keyboard' => false,
        'resize_keyboard' => true,
        'selective' => true,
    ], true);
    $response = [
        'chat_id' => $chat_id,
        'parse_mode' => 'HTML',
        'text' => $text,
        'reply_markup' => $buttons,
    ];
    sendit($response, "sendMessage");
}

function sendImage($chat_id, $caption){
    $files = glob("./img/" . '/*.*'); 
    $file = array_rand($files);
    $file = substr($files[$file], 1);
    $file = "$file"; //абсолютный путь к файлу
    $buttons =  json_encode([
        "keyboard" => [
            [["text" => "/Еще!"]]
        ],
        'one_time_keyboard' => false,
        'resize_keyboard' => true,
        'selective' => true,
    ], true);
    $response = [
        'chat_id' => $chat_id,
        'photo' => $file,
        'caption' => $caption,
        'parse_mode' => 'HTML',
        'reply_markup' => $buttons,
    ];
    sendit($response, "sendPhoto");

}
