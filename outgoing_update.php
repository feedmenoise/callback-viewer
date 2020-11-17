#!/usr/bin/php

<?php
		$call_type	= $argv[1];
        $callid 	= $argv[2];
        $number 	= $argv[3];
        $agent  	= $argv[4];
        $time   	= $argv[5];
        $status 	= $argv[6];

        $servername = "localhost";
        $database = "asterisk";
        $username = "root";
        $password = "zhopa13";

        #преобразовать number в %XXYYYZZZZ

        $time = date('d-m-y H:i:s', strtotime($time));
        $time = date('Y-m-d H:i:s', strtotime($time));

        #answered
        if ($call_type == "out" && $status == "ANSWER" && $number != "") {
            $flag=2;
            if (strpos($number, "+38") !== false) {
                $number = str_replace("+38", "", $number);
                #echo "тут должен быть вырезан +38: $number <br>";
            }

            $connection = new mysqli($servername,$username,$password,$database);
            $update_query = 'UPDATE callbacklog SET agent="'.$agent.'",callback_callid="'.$callid.'",time_2="'.$time.'",flag="2" WHERE number LIKE "%'.$number.'" AND FLAG != "2" AND FLAG != "8"';
            $result=$connection->query($update_query);
            echo 'agent: $agent , number = $number :  from Out and Answer';

        }

        #BUSY
        else if ($call_type == "out" && $status == "BUSY") {
            $flag=3;
            if (strpos($number, "+38") !== false) {
                $number = str_replace("+38", "", $number);
                #echo "тут должен быть вырезан +38: $number <br>";
            }
            $connection = new mysqli($servername,$username,$password,$database);
            $update_query = 'UPDATE callbacklog SET agent="'.$agent.'",time_2="'.$time.'",flag="'.$flag.'" WHERE number LIKE "%'.$number.'" AND FLAG != "2" AND FLAG != "8"';
            $result=$connection->query($update_query);
            echo 'agent: $agent , number = $number :  from Out and Busy';

        }

        #NOANSWER
        else if ($call_type == "out" && $status == "NOANSWER") {
            $flag=4;
            if (strpos($number, "+38") !== false) {
                $number = str_replace("+38", "", $number);
                #echo "тут должен быть вырезан +38: $number <br>";
            }
            $connection = new mysqli($servername,$username,$password,$database);
            $update_query = 'UPDATE callbacklog SET agent="'.$agent.'",time_2="'.$time.'",flag="'.$flag.'" WHERE number LIKE "%'.$number.'" AND FLAG != "2" AND FLAG != "8"';
            $result=$connection->query($update_query);
            echo 'agent: $agent , number = $number :  from Out and Noanswer';
        }

        #CANCEL
        else if($call_type == "out" && $status == "CANCEL") {
            $flag=5;
            if (strpos($number, "+38") !== false) {
                $number = str_replace("+38", "", $number);
                #echo "тут должен быть вырезан +38: $number <br>";
            }
            $connection = new mysqli($servername,$username,$password,$database);
            $update_query = 'UPDATE callbacklog SET agent="'.$agent.'",time_2="'.$time.'",flag="'.$flag.'" WHERE number LIKE "%'.$number.'" AND FLAG != "2" AND FLAG != "8"';
            $result=$connection->query($update_query);
            echo 'agent: $agent , number = $number :  from Out and Cancel';
        }
        #HANGUP
        else if($call_type == "out" && $status == "HANGUP") {
            $flag=6;
            if (strpos($number, "+38") !== false) {
                $number = str_replace("+38", "", $number);
                #echo "тут должен быть вырезан +38: $number <br>";
            }
            $connection = new mysqli($servername,$username,$password,$database);
            $update_query = 'UPDATE callbacklog SET agent="'.$agent.'",time_2="'.$time.'",flag="'.$flag.'" WHERE number LIKE "%'.$number.'" AND FLAG != "2" AND FLAG != "8"';
            $result=$connection->query($update_query);
            echo 'agent: $agent , number = $number :   from Out and Hangup';
        }

        else if($call_type == "out" && $status == "CHANUNAVAIL") {
            $flag=7;
            if (strpos($number, "+38") !== false) {
                $number = str_replace("+38", "", $number);
                #echo "тут должен быть вырезан +38: $number <br>";
            }
            $connection = new mysqli($servername,$username,$password,$database);
            $update_query = 'UPDATE callbacklog SET agent="'.$agent.'",time_2="'.$time.'",flag="'.$flag.'" WHERE number LIKE "%'.$number.'" AND FLAG != "2" AND FLAG != "8"';
            $result=$connection->query($update_query);
            echo 'agent: $agent , number = $number :   from Out and Chanunavail';
        }



        #unanswered
        else if ($call_type == "in" && $status == "TRUE") {
            $flag=1;
        	if (strpos($number, "+38") !== false) {
                        $number = str_replace("+38", "", $number);
                        #echo "тут должен быть вырезан +38: $number <br>";
            }

            $conn = new mysqli($servername, $username, $password, $database);
            $stmt = $conn->prepare("INSERT INTO callbacklog (time_1,callid,queuename,number,flag) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss",$time, $callid, $agent, $number, $flag);
            $stmt->execute();
                #echo $callid.": must be added to callbacklog </br>";
            $stmt->close();
            $conn->close();
            echo 'agent: '.$agent.' , number = $number :   from IN and TRUE';

        }

        
        #дозовнился
        else if ($call_type == "in" && $status == "FALSE") {
            $flag=8;
            if (strpos($number, "+38") !== false) {
                $number = str_replace("+38", "", $number);
                #echo "тут должен быть вырезан +38: $number <br>";
            }

            $conn = new mysqli($servername,$username,$password,$database);
            $connection = new mysqli($servername,$username,$password,$database);
            $update_query = 'UPDATE callbacklog SET callback_callid="'.$callid.'",time_2="'.$time.'",flag="'.$flag.'" WHERE number LIKE "%'.$number.'" AND FLAG != "2" AND FLAG != "8"';
            $result=$connection->query($update_query);
            
            echo 'agent: $agent , number = $number :   from in and FALSE';

        }


        else {
            echo 'agent: '.$agent.' , number = '.$number.' :   никуда не попал';
        }


?>

