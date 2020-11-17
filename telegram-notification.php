#!/usr/bin/php -q

<?php

$servername = "localhost";
$database = "asterisk";
$username = "root";
$password = "zhopa13";

$chatid = $argv[1];
$queue = $argv[2];
$hours = $argv[3];

$now = date('Y-m-d H:i:s');
$new_time = date('Y-m-d H:i:s', strtotime("-$hours hours", strtotime($now)));

$connection = new mysqli($servername,$username,$password,$database);
$queryAll = "select COUNT(*) from callbacklog where time_1 between '$new_time' and '$now' and queuename = '$queue'";
$querySelf =  "select COUNT(*) from callbacklog where time_1 between '$new_time' and '$now' and flag='8' and queuename = '$queue'";
$queryCallback = "select COUNT(*) from callbacklog where time_1 between '$new_time' and '$now' and flag='2' and queuename = '$queue'";
$queryNotAnswered = "select COUNT(*) from callbacklog where time_1 between '$new_time' and '$now' and flag != '2' and flag != '8' and queuename = '$queue'";
$all=mysqli_fetch_array($connection->query($queryAll));
$self = mysqli_fetch_array($connection->query($querySelf));
$callback = mysqli_fetch_array($connection->query($queryCallback));
$notAnswered = mysqli_fetch_array($connection->query($queryNotAnswered));

$msg = "";

if ($notAnswered[0] != 0) {

        $msg .= "<b>Отдел</b> : " . $queue . PHP_EOL;
        $msg .= "<b>Период</b> : $new_time - $now" . PHP_EOL;
        $msg .= "<b>Всего пропущенных</b> : " . $all[0] . PHP_EOL;
        $msg .= "<b>Дозвонились сами</b> : " . $self[0] . PHP_EOL;
        $msg .= "<b>Обработанных</b> : " . $callback[0] . PHP_EOL;
        $msg .= "<b>Не обработанных</b> : " . $notAnswered[0] . PHP_EOL;

        $getNumbersQuery = "select * from callbacklog where time_1 between '$new_time' and '$now' and flag != '2' and flag != '8' and queuename = '$queue'";
        $getNumbersArray = $connection->query($getNumbersQuery);

       while ($row = mysqli_fetch_array($getNumbersArray)) {
                $msg .= "       <i>".$row['number']."</i> - ";
                $msg .= "<i>".$row['time_1']."</i>".PHP_EOL;
        } 

        sendMsg($chatid, $msg);
        #echo $msg;
} 

function sendMsg($chatid, $msg) {
        $argv[1] = $chatid;
        $argv[2] = $msg;
        include("/etc/asterisk/custom_scripts/tg-bot.php");
}

?>
