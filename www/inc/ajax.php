<?php

require("ami.class.php");
include("config.php");
if (isset($_POST["method"]))  {
	$method = $_POST["method"];

	switch($method) {

		case "createMessage" :
		createMessage($_POST["message"]);
		break;

		case "applyMessage" : 
		applyMessage($_POST["message"]);
		break;

		case "saveMessage" :
		saveMessage($_POST["message"]);
		break;

		case "clearMessage" :
		clearMessage($_POST["message"]);
		break;

		case "applyFromTemplate" :
		applyFromTemplate($_POST["id"]);
		break;

		case "dongleReset" : 
		dongleReset($_POST["dongle"]);
		break;

		case "deleteTemplate" :
		deleteTemplate($_POST['id']);
		break;

		case "playTemplateModal" :
		playTemplateModal($_POST['id']);
		break;

		case "playRecModal" :
		playRecModal($_POST['id']);
		break;

		case "templateMessages" :
		echo messageFromTemplate($_POST['company']);
		break;

		case "getAbon" :
		echo getInfoByPhone($_POST['number'], $_POST['company']);
		break;

		case "getPeerName" :
		echo getPeerName($_POST['peer']);
		break;

		case "install" :
		echo installScript($_POST['config']);
		break;

		default :
		echo "pososi";
		break;

	}
}

function installScript($config) {
	$config = json_decode( $config );
	$fp = fopen('config.php', 'w+') or die('Cannot create config file, check permissions.');

	$test = "<?php 
	define('AMI_HOST','$config->ami_host');
	define('AMI_USER','$config->ami_user');	
	define('AMI_PASS','$config->ami_pass');
	define('AMI_PORT','$config->ami_port');
	define('AMI_TIMEOUT','$config->ami_timeout');
	
	# DB
	
	define('DB_HOST','$config->db_host');
	define('DB_USER','$config->db_user');
	define('DB_PASS','$config->db_pswd');
	define('DB_NAME','$config->db_name');
	
	# API KEYS
	
	define('US1_API', '$config->us1_api');
	define('US2_API', '$config->us2_api');
	define('YANDEX_API', '$config->yandex_tts');
	
	# USLINKS
	
	define('US1_LINK', '$config->us1_link');
	define('US2_LINK', '$config->us2_link');
	
	?>";

	fwrite($fp, $test);
	fclose($fp);

	if ( $config->create_db == true ) {
		$result = createDB();
	}
	return $result;
}

function createDB() {
	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS);
	$connect->set_charset("utf8");
	$sql = file_get_contents('../db_init.sql');
	$result=$connect->multi_query($sql);
	
	return $result;
}

function getInfoByPhone($number, $company) {
	$number = json_decode( $number );
	$company = json_decode( $company );
	try {
		switch ($company) {
			case "ggt" : $json_get_id = @file_get_contents(US1_LINK . 'api.php?key='.US1_API.'&cat=customer&subcat=get_abon_id&data_typer=phone&data_value=' . $number); break;
			case "gl" : $json_get_id = file_get_contents(US2_LINK . 'api.php?key='.US2_API.'&cat=customer&subcat=get_abon_id&data_typer=phone&data_value=' . $number); break;
		}
	} catch (Exception $e ) {
		exit(0);
	}


	$obj = json_decode($json_get_id);
	if (isset($obj->Id)) {
		$id_array = $obj->Id;

		if (is_array($id_array)) {
			foreach ($id_array as $key => $id) {
				$result_arrays[$key] = getInfoById($id, $company);
			}
		}

		else if (is_int($id_array)) {
			$result_arrays[0] = getInfoById($id_array, $company);
		} 
	} 
	else 
		exit(0);

	$result_arrays = json_encode($result_arrays);
	return $result_arrays;
}

function getInfoById($id, $company) {
	$result = array();
	$result["id"] = $id;

	switch ($company) {
		case "ggt" : $json_get_user_data = file_get_contents(US1_LINK . 'api.php?key='.US1_API.'&cat=customer&subcat=get_data&customer_id='.$id); break;
		case "gl" : $json_get_user_data = file_get_contents(US2_LINK . 'api.php?key='.US2_API.'&cat=customer&subcat=get_data&customer_id='.$id); break;
	}
	$obj_data = json_decode($json_get_user_data);
	$data_array = $obj_data->Data;
	if (isset($data_array->login)) {
		$result["login"] = $data_array->login;
	} 
	if (!isset($data_array->login) && isset($data_array->account_number)) {
		$result["login"] = $data_array->account_number;
	}
	if (isset($data_array->full_name))
		$result["full_name"] = $data_array->full_name;

	switch($company) {
		case "ggt": $result["url_to_us"] = US1_LINK . "oper/?core_section=customer&action=show&id=" . $id; break;
		case "gl": $result["url_to_us"] = US2_LINK . "oper/users.php?type=show&ret=house&code=" . $id; break;
	}

	return $result;
}


function getPeerName($peer) {
	$peer = json_decode($peer);
	$ami = new ami();
	$peerAmi = $ami->getpeerinfo($peer);
	if ($peerAmi['Response'] == "Success") $result = $peerAmi['Description'];
	else $result = print_r($peerAmi);

	return $result;
}

function messageFromTemplate($company) {

	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$connect->set_charset("utf8");
	$query = "SELECT * FROM alarm WHERE company='$company' ORDER BY flag DESC";

	$result=$connect->query($query);

	$json_templates;

	while ($row = mysqli_fetch_array($result)) {
		$template = [
			"id" => $row['id'],
			"text" => $row['text'],
			"filename" => $row['filename'],
			"flag" => $row['flag']
		];

		$json_templates[] = $template;
	}

	$json_templates = json_encode($json_templates);
	return $json_templates;
}


function playRecModal($id) {
	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$connect->set_charset("utf8");

	$row = $connect->query("SELECT * FROM callbacklog WHERE id=$id");
	$row = mysqli_fetch_array($row);

	$tmpURL ='inc/dl.class.php?f=[_file]';

	$rec['filename'] = $row['callback_callid'] . '.mp3';
	$rec['path'] = '/rec/1/mp3/' .  $rec['filename'];

	if (file_exists($rec['path']) && preg_match('/(.*)\.mp3$/i', $rec['filename'])) {
		$url = str_replace('[_file]', base64_encode($rec['path']), $tmpURL);

	} 

	$out = '
	<div class="modal-header bg-dark text-white">
	<h5 class="modal-title">'.$row['number'].'</h5>
	<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
	<span aria-hidden="true">&times;</span>
	</button>
	</div>
	<div class="modal-body">
	<div class="audio">
	<center>
	<audio controls preload="none" id="audio"><source src="'.$url.'" type="audio/wav" ></audio> <br>Speed: <button onclick="setPlaySpeed(\'1\')" type="button">x1</button> 
		<button onclick="setPlaySpeed(\'2\')" type="button">x2</button>  
		<button onclick="setPlaySpeed(\'3\')" type="button">x3</button> <br>
	</center>
	</div>
	</div>
	<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-outline-secondary" type="button" data-dismiss="modal"><span data-feather="x"></span>Закрыть</button>
	</div>';
	echo $out;
}

function playTemplateModal($id) {
	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$connect->set_charset("utf8");

	$template = $connect->query("SELECT * FROM alarm WHERE id=$id");
	$template = mysqli_fetch_array($template);
	$filename = $template['filename'];

	$tmpURL ="inc/dl.class.php?f=[_file]";

	$rec['filename'] = $filename . '.wav';
	$rec['path'] = '/rec/alarm_messages/wav/' .  $rec['filename'];

	if (file_exists($rec['path']) && preg_match('/(.*)\.wav$/i', $rec['filename'])) {
		$url = str_replace('[_file]', base64_encode($rec['path']), $tmpURL);

	} 

	$out = '
	<div class="modal-header bg-dark text-white">
	<h5 class="modal-title">'.$filename.'</h5>
	<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
	<span aria-hidden="true">&times;</span>
	</button>
	</div>
	<div class="modal-body">
	<div class="audio">
	<audio controls preload="none"><source src="'.$url.'" type="audio/wav"></audio><br>
	</div>
	</div>
	<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-outline-secondary" type="button" data-dismiss="modal"><span data-feather="x"></span>Закрыть</button>
	</div>';
	echo $out;
}

function deleteTemplate($id) {
	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$connect->set_charset("utf8");
	$id = json_decode($id);

	$connect->query("DELETE FROM alarm WHERE id=$id");
}

function applyFromTemplate($id) {
	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$connect->set_charset("utf8");

	$id = json_decode($id);

	$template = $connect->query("SELECT * FROM alarm WHERE id=$id");
	$template = mysqli_fetch_array($template);

	if ($template['company'] == 1) $context = "ivr_avariynoe";
	else if ($template['company'] == 2 ) $context = "ivr_avariynoe_gorodok";
	else echo "something wrong with message";

	$template['context'] = $context;
	$template = json_encode($template);
	$connect->query("UPDATE alarm SET flag=0");
	$connect->query("UPDATE alarm SET flag=1 WHERE id=$id");
	
	applyMessage($template);

}

//прегрузка модема
function dongleReset($dongle) {
	$dongle = json_decode($dongle);
	$ami = new ami();
	$ami->sendcommand2asterWrite("DongleReset", $dongle);
}

//Создать сообщение
function createMessage($message) {

	$message = json_decode($message);
	$text = $message->text;
	$filename = date_timestamp_get(date_create());

	$prefix = '/rec/alarm_messages/';
	$speaker='oksana';
	$emotion='good';
	$lang='ru-RU';

	exec('mkdir -p /rec/alarm_messages/wav | mkdir -p /rec/alarm_messages/alaw | curl "https://tts.voicetech.yandex.net/generate?format=wav&lang='.$lang.'&speaker='.$speaker.'&emotion='.$emotion.'&key='.YANDEX_API.'" -G --data-urlencode "text='.$text.'" > /rec/alarm_messages/wav/'.$filename.'.wav');
	exec('/usr/bin/sox /rec/alarm_messages/wav/'.$filename.'.wav -t RAW -e a-law -r 8000 -c 1 -b 8 /rec/alarm_messages/alaw/'.$filename.'_converted.alaw');

	$tmpURL ="inc/dl.class.php?f=[_file]";

	$rec['filename'] = $filename . '.wav';
	$rec['path'] = '/rec/alarm_messages/wav/' .  $rec['filename'];

	if (file_exists($rec['path']) && preg_match('/(.*)\.wav$/i', $rec['filename'])) {
		$answer['url'] = str_replace('[_file]', base64_encode($rec['path']), $tmpURL);

	} 

	$answer['text'] = $text;
	$answer['user'] = $_SERVER['REMOTE_ADDR'];
	$answer['file_name'] = $filename;
	$answer = json_encode($answer);
	echo $answer;
}


//Убрать текущее сообщение
function clearMessage($message) {

	$message = json_decode($message);
	$context = $message->context;
	$ami = new ami();
	//$context = "ivr_avariynoe";
	$exten = "s";
	$prior = "1";
	$app ="NoOp";
	$appdata = "Alarm Message context";
	$replace = "true";

	$prior_for_note = "2";
	$app_for_note = "NoOp";
	$text = "Message in plain text will be here";

	$ami->sendExtenToDialplan($context,$exten,$prior,$app,$appdata,$replace);
	$ami->sendExtenToDialplan($context,$exten,$prior_for_note,$app_for_note,$text,$replace);

	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$connect->set_charset("utf8");
	$connect->query('UPDATE alarm SET flag=0 WHERE flag=1');

}


//Отправка сообщения в астериск
function applyMessage($message) {

	$message = json_decode($message);
	$text = $message->text;
	$filename = $message->filename;
	$context = $message->context;

	$ami = new ami();
	//$context = "ivr_avariynoe";
	$exten = "s";
	$prior = "1";
	$app ="Playback";
	$appdata = "/rec/alarm_messages/alaw/".$filename."_converted";
	$replace = "true";

	$prior_for_note = "2";
	$app_for_note = "NoOp";
	$text = "Start message:".$text;
	$text = str_replace("\n", " ", $text);
	$ami->sendExtenToDialplan($context,$exten,$prior,$app,$appdata,$replace);
	$ami->sendExtenToDialplan($context,$exten,$prior_for_note,$app_for_note,$text,$replace);

	$tmpURL ="inc/dl.class.php?f=[_file]";

	$rec['filename'] = $filename . '.wav';
	$rec['path'] = '/rec/alarm_messages/wav/' .  $rec['filename'];

	if (file_exists($rec['path']) && preg_match('/(.*)\.wav$/i', $rec['filename'])) {
		$answer['url'] = str_replace('[_file]', base64_encode($rec['path']), $tmpURL);

	} 

	$answer['text'] = $message->text;
	$answer['user'] = $_SERVER['REMOTE_ADDR'];
	$answer['context'] = $context;
	$answer = json_encode($answer);
	echo $answer;

}

//Сохранение сообщения в бд 
function saveMessage($message) {

	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	$connect->set_charset("utf8");

	$message = json_decode($message);
	$text = $message->text;
	$filename = $message->filename;
	$company = $message->company;
	$flag = 0;

	if (mysqli_fetch_array($connect->query("SELECT * FROM `alarm` WHERE text='$text'"))) {
		echo "Шаблон уже существует!";
	}
	else {
		
		$stmt = mysqli_prepare($connect, "INSERT INTO `alarm`(`text`, `filename`, `flag`, `company`) VALUES (?, ?, ?, ?)");
		mysqli_stmt_bind_param($stmt, "ssii", $text, $filename, $flag, $company);	
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
	mysqli_close($connect);

}

?>
