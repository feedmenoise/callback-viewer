#!/usr/bin/php -q
<?php

if (isset($argv[1]) && isset($argv[2])) {
        $token = " ";
        $chat_id = $argv[1];
        $text = $argv[2];

        $url = "https://api.telegram.org/bot$token/sendMessage";
        $params = array(
		'parse_mode' => "html",
                'chat_id' => $chat_id,
                'text' => $text,
        );

        $result = @file_get_contents($url, false, stream_context_create(array(
                'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($params)
                )
        )));

        @logging($chat_id, $text, $result);
} else {
        help();
}

function logging($chat_id, $text, $result){
        $date = date('Y-m-d H:i:s');
        $logdir = '/var/log';
        $msg == "";
        $result = get_object_vars(json_decode($result));

        $result = get_object_vars($result['result']);
        $msg = $result['text'];
        $chatName = get_object_vars($result['chat']);
        $chatName = $chatName['title'];

        if (isset($chat_id) && isset($text) && $msg != "") {
                $log = "$date | chat ID: $chat_id | chat name : $chatName | sent message : \"$text\" | recieve message : \"$msg\"".PHP_EOL;
        } elseif (isset($chat_id) && isset($text) && $msg == null) {
                $log = "$date | Something wrong with output on chat_id : $chat_id | text : $text".PHP_EOL;
        } else {
                $log = "$date | Something wrong with arguments".PHP_EOL;
        }

        $fd = fopen("$logdir/tg-bot", 'a') or die("не удалось создать файл");
        fwrite($fd, $log);
        fclose($fd);
}

function help() {
        echo "\t Example of use:".PHP_EOL."\t\t./tg-bot.php CHAT_ID MESSAGE".PHP_EOL;
        @logging();
}

?>

