<?php
class ami {

    function connectToAmi() {

        $fp=fsockopen(AMI_HOST, AMI_PORT, $errno, $errstr, AMI_TIMEOUT);
        if (!$fp) {
            echo "ERROR " . $errstr . "--" . $errno;

        }
        else {
        //echo "Connection to Asterisk";
            $login="Action: login\r\n";
            $login .= "Username: ". AMI_USER.PHP_EOL;
            $login .= "Secret: ". AMI_PASS.PHP_EOL;
            $login .= "Events: Off\r\n";
            $login .="\r\n";
            fwrite($fp, $login);
            $asterVer = fgets($fp);
            $response = fgets($fp);
            $message = fgets($fp);

            if (substr($message,0,8)=="Message:"){
                $authstat=trim(substr($message,9));
                if (!$authstat=="Authentication accepted") {
                    echo "Authentication error\r\n";
                    fclose($fp);
                    return null;
                }
                else {
                    #echo "Authentication accepted";
                    return $fp;

                }
            }
        }
    }

    function getpeerinfo($peer) {
        
        $fp = $this->connectToAmi();

        $status_peer = "Action: SIPshowpeer\r\n";
        $status_peer .= "Peer: $peer\r\n";
        $status_peer .= "\r\n";

        fwrite($fp, $status_peer);
        fgets($fp); 
        $response = trim(fgets($fp));
        $response = explode(": ", $response);
        $result['Response'] = $response[1];

        if ($response[1] == "Success") {
            for ($i = 0 ; $i < 61; $i++) {
                $line=trim(fgets($fp));
                $line = explode(": ", $line);
                $param = $line[0];
                $data = $line[1];
                $result[$param] = $data;
            }
        } else {
            $message= trim(fgets($fp));
            $message = explode(": ", $message);
            #$result['Message'] = $message[1];

        }

        fclose($fp);

        return $result;

    }

    function sendcommand2asterRead($command, $dev){

        $fp = $this->connectToAmi();

        $status_peer = "Action: $command\r\n";
        $status_peer .= "Device: $dev\r\n";
        $status_peer .= "\r\n";
        fwrite($fp, $status_peer);
        $line=trim(fgets($fp));
        $line_response=trim(fgets($fp));
        $line_privileges=trim(fgets($fp));
        $line_status=trim(fgets($fp));

        while ($line!="EventList: Complete"){
            $result[]=$line;
            $line=trim(fgets($fp));
        }

        fclose($fp);

        return $result;

    }

    function sendcommand2asterWrite($command, $dev){

        $fp = $this->connectToAmi();

        $status_peer = "Action: $command\r\n";
        $status_peer .= "Device: $dev\r\n";
        $status_peer .= "\r\n";
        fwrite($fp, $status_peer);
        $line=trim(fgets($fp));
        $line_response=trim(fgets($fp));
        $line_privileges=trim(fgets($fp));
        $line_status=trim(fgets($fp));
        fclose($fp);

    }

    function sendExtenToDialplan($context,$exten,$prior,$app,$appdata,$replace) {

        $fp = $this->connectToAmi();

        $status_peer = "Action: DialplanExtensionAdd\r\n";
        $status_peer .= "Context: $context\r\n";
        $status_peer .= "Extension: $exten\r\n";
        $status_peer .= "Priority: $prior\r\n";
        $status_peer .= "Application: $app\r\n";
        $status_peer .= "ApplicationData: $appdata\r\n";
        $status_peer .= "Replace: $replace\r\n";
        $status_peer .= "\r\n";
        fwrite($fp, $status_peer);
        fclose($fp);

    }   

    function showDialPlan($context,$exten) {

        $fp = $this->connectToAmi();

        $status_peer = "Action: ShowDialPlan\r\n";
        $status_peer .= "Context: ".$context."\r\n";
        $status_peer .= "Extension: ".$exten."\r\n";
        $status_peer .= "\r\n";
        fwrite($fp, $status_peer);
        $line=trim(fgets($fp));
        $line_response=trim(fgets($fp));
        $line_privileges=trim(fgets($fp));
        $line_status=trim(fgets($fp));

        while ($line!="EventList: Complete"){
            $result[]=$line;
                #echo $line.PHP_EOL;
            $line=trim(fgets($fp));
        }

        fclose($fp);

        return $result;
    }

    function getDonglesList() {

        $fp = $this->connectToAmi();

        $status_peer = "Action: DongleShowDevices\r\n";
        $status_peer .= "\r\n";
        fwrite($fp, $status_peer);
        $line=trim(fgets($fp));
        $line_response=trim(fgets($fp));
        $line_privileges=trim(fgets($fp));
        $line_status=trim(fgets($fp));

        while ($line!="EventList: Complete"){
            if (strpos($line, 'Device:') !== false) {
                $line = explode(": ", trim($line));
                $result[]=$line[1];

            }
            $line=trim(fgets($fp));
        }

        fclose($fp);

        return $result;

    }

}
?>










