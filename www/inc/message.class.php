<?php 

class message {

	

	function createMessage() {

		$out = "
		<form id=\"createMessageForm\">
		<div class=\"form-row\">
		<div class=\"form-group col-md-10 mb-8\">
		<label>Сообщение для синтеза</label>
		<textarea name=\"text\" id=\"message\" class=\"form-control\" placeholder=\"Текст аварийного сообщения...\" rows=\"6\" required></textarea>
		</div>
		</div>
		<input type=\"submit\" value=\"Отправить\" id=\"submitMessage\" class=\"btn-sm btn-success\">
		</form>
		<div id=\"loadingMessage\" class=\"justify-content-center hide_content\">
		<div class=\"spinner-border\" style=\"width: 10rem; height: 10rem;\" role=\"status\">
		<span class=\"sr-only\">Loading...</span>
		</div>
		</div>
		";

		return $out;
	}

	function currentMessageForm ($company) {

		$out = "<div class=\"col-md-auto col-lg-auto ".$company."\"><div class=\"current-message-form\"><h5>Текущее Сообщение </h5>";

		if ($company == "Gigabit") {
			$img = "logo-gigabit-sm.png";
		}
		else if ($company = "Gorodok") {
			$img = "logo-gorodok-sm.png";

		}

		$out .= "<img src=\"img\\".$img."\">";
		
		$player = "";
		$cur_message = "";

		$message=$this->currentMessage($company); //проверка состояния

		if ($message[0] != "") {

			$tmpRec ='<audio controls class="player '.$company.' " preload="none" src="inc/dl.class.php?f=[_file]" type="audio/wav"></audio>';

			$message[0] = str_replace("AppData: ", "", $message[0]);
			$message[0] = str_replace("alaw", "wav", $message[0]) . ".wav";
			$message[0] = str_replace("_converted", "", $message[0]);

			$cur_message = str_replace("AppData: Start message:","",$message[1]);

			if (file_exists($message[0])) {
				$player = str_replace('[_file]', base64_encode($message[0]), $tmpRec);

			} 
			$status = "Включено";
			$button = "<button type=\"button\" class=\"btn btn-sm btn-success clearMessageBtn current  ".$company."\">Выключить</button>";
		}
		else {
			$status = "Выключено";
			$player = "<audio controls class=\"player hide_content ".$company."\" preload=\"none\" src=\"\" type=\"audio/wav\"></audio>";
			$button = "<button type=\"button\" class=\"btn btn-sm btn-success clearMessageBtn current hide_content ".$company."\">Выключить</button>";

		}

		$out .= "<div class=\"current-message-status ".$company."\">$status</div> 
		<div class=\"current-message-text ".$company."\">$cur_message</div> 
		<div class=\"current-audio ".$company."\">$player</div>
		$button 
		</div>
		</div>";

		return $out;
	}

	function currentMessage($company) {
		if ($company == "Gigabit") {
			$context = "ivr_avariynoe";
			$exten = "s";
		}
		else if ($company = "Gorodok") {
			$context = "ivr_avariynoe_gorodok";
			$exten = "s";
		}

		$ami = new ami();
		$result[] = "";

		$current_exten = $ami->showDialPlan($context,$exten);

		foreach ($current_exten as $key=>$val){
			if (strpos($val, '/rec/alarm_messages/alaw/') !== false){
				#var_dump($val);
				$result[0] = $val;
			}

			if (strpos($val, 'Start message:') !== false )
				$result[1] = $val;
		}

		return $result;
	}


	function messageFromTemplate() {
		$out = "<div class=\"message-container\">
		<ul class=\"message-responsive-table\">";

//		$connect = new mysqli("localhost","root","zhopa13","asterisk");
//		$connect->set_charset("utf8");
//		$query = "SELECT * FROM alarm ORDER BY flag DESC";
//
//		$result=$connect->query($query);
//
//		while($row= mysqli_fetch_array($result)) {
//			$out .= ($row['flag'] == "1") ? "<li class=\"message-table-row \" style=\"background: #7FFFD4;\" >" : "<li class=\"message-table-row\">";
//			$out .= "
//			<div class=\"message-col message-col-1\" >".$row['text']."</div>
//			<div class=\"message-col message-col-2\" >
//			<button type=\"button\" class=\"btn btn-sm btn-outline-info play-template\" data-toggle=\"modal\" data-target=\"#exampleModalCenter\" data-id=\"".$row['id']."\"><span data-feather=\"play\"></span></button>
//			<button type=\"button\"" . (($row['flag'] == "1") ? "class=\"btn btn-sm btn-outline-success hide_content\"" : "class=\"btn btn-sm btn-outline-success\"") .
//			 "onclick=\"applyTemplate('".$row['id']."')\"><span data-feather=\"send\"></span></button>" . (($row['flag'] == "1") ? "<button type=\"button\" class=\"btn btn-sm btn-outline-danger clearMessageBtn\"><span data-feather=\"slash\"></span></button>" :
//			 "<button type=\"button\" class=\"btn btn-sm btn-outline-danger\" onclick=\"deleteTemplate('".$row['id']."')\"><span data-feather=\"trash-2\"></span></button>") .
//			"</div>
//			</li>";
//		}
		$out .= "</ul></div>";

		return $out;
	}

	function currentTab($tab) {

		$currentTab = 
		(($tab == "current") ? "<div class=\"tab-pane fade show active\"" : "<div class=\"tab-pane fade\"") . "id=\"current\" role=\"tabpanel\" aria-labelledby=\"current-tab\">%current%</div> " .
		(($tab == "template") ? "<div class=\"tab-pane fade show active\"" : "<div class=\"tab-pane fade\"") . "id=\"template\" role=\"tabpanel\" aria-labelledby=\"template-tab\">%templates%</div>" .
		(($tab == "new") ? "<div class=\"tab-pane fade show active\"" : "<div class=\"tab-pane fade\"") . "id=\"new\" role=\"tabpanel\" aria-labelledby=\"new-tab\">";

		return $currentTab;
	}

	function showContent($stat) {

		if(isset($_POST["clear"]))
			$this->clearMessage();
		$ln = new parser("message.tpl");

		$menu = new nav();

		if(isset($_GET["sp"])){
			$msgMenu = $menu->showMessageMenu($_GET["sp"]);			
			$tab = $this->currentTab($_GET["sp"]);
		}
		else {
			$msgMenu = $menu->showMessageMenu("current");
			$tab = $this->currentTab("current");
		}

		if ($stat == "new")
			$content = $this->checkMessage("test","test2");
		else {
			$content = $this->createMessage();

		}
		$currentMessageform = $this->currentMessageForm("Gigabit");
		$currentMessageform .= "<hr>";
		$currentMessageform .= $this->currentMessageForm("Gorodok");
		$template = $this->messageFromTemplate();
		$ln->get_tpl();
		$ln->set_tpl("%tab%", $tab);
		$ln->set_tpl("%message%", $content);
		$ln->set_tpl("%current%", $currentMessageform);
		$ln->set_tpl("%msgMenu%", $msgMenu);
		$ln->set_tpl("%templates%", $template);

		return $ln->tpl_parse();
	}
}
?>