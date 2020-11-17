<?php 
ob_start("ob_gzhandler");
session_start();
ini_set('display_errors', 0);

require_once "./inc/parser.class.php";
require_once "./inc/nav.class.php";

$remoteIP = $_SERVER['REMOTE_ADDR'];
$time = date("Y-m-d H:i:s"); 
$style = "<div class=\"container rtfy\">%cheza%</div>";
$leftmenu ="";
$page="install";

$content = '
<div class="message-row">
    <div class="message-header ">
    	<h5>Database</h5>
    </div>
    
    <div class="col-12">
     <label>Host</label>
	 <input class="form-control" maxlength="50" size="15" id="db-host" value="mariadb" style="background-color: #eee;">
	 
     <label>Database user</label>
     <input class="form-control" type="text" maxlength="50" size="15" id="db-user" value="root" style="background-color: #eee;">

     <label>Password</label>
     <input class="form-control" type="text" maxlength="50" size="15" id="db-pswd" value="zhopa13" style="background-color: #eee;">

     <label>Database name</label>
     <input class="form-control" type="text" maxlength="50" size="15" id="db-name" value="asterisk" style="background-color: #eee;">

     <label>Create database ?</label>
		<div class="form_toggle">  
			<div class="form_toggle-item item-1">
				<input id="fid-1" type="radio" name="radio" value="create-db-no">
					<label for="fid-1">No</label>
			</div><div class="form_toggle-item item-2">
				<input id="fid-2" type="radio" name="radio" value="create-db-yes" checked>
					<label for="fid-2">Yes</label>
			</div>
		</div>
		<br>
	</div>
</div>

<div class="message-row">
	<div class="message-header ">
		<h5>Asterisk AMI</h5>
	</div>
	    <div class="col-12">
			<label>AMI_HOST</label>
			<input class="form-control" maxlength="50" size="15" id="ami-host" value="10.30.60.56" style="background-color: #eee;">

			<label>AMI_USER</label>
			<input class="form-control" maxlength="50" size="15" id="ami-user" value="callbacklog" style="background-color: #eee;">

			<label>AMI_PASS</label>
			<input class="form-control" maxlength="50" size="15" id="ami-pass" value="huilo13" style="background-color: #eee;">

			<label>AMI_PORT</label>
			<input class="form-control" maxlength="50" size="15" id="ami-port" value="5038" style="background-color: #eee;">

			<label>AMI_TIMEOUT</label>
			<input class="form-control" maxlength="50" size="15" id="ami-timeout" value="5" style="background-color: #eee;">
			<br>
		</div>
</div>

<div class="message-row">
	<div class="message-header ">
		<h5>API</h5>
	</div>
	    <div class="col-12">
	    	<label>Yandex TTS API key</label>
			<input class="form-control" maxlength="50" size="15" id="yandex" value="" style="background-color: #eee;">

			<b><label>Userside#1 config</label></b><br>

			<label>US1_LINK</label>
			<input class="form-control" maxlength="50" size="15" id="us1-link" value="https://us.gigabit.zp.ua/" style="background-color: #eee;">

			<label>US1_API</label>
			<input class="form-control" maxlength="50" size="15" id="us1-api" value="" style="background-color: #eee;">

			<br>
			<b><label>Userside#2 config</label></b><br>

			<label>US2_LINK</label>
			<input class="form-control" maxlength="50" size="15" id="us2-link" value="http://billing.gorodok.zp.ua/" style="background-color: #eee;">

			<label>US2_API</label>
			<input class="form-control" maxlength="50" size="15" id="us2-api" value="" style="background-color: #eee;">
		</div>
		<br>
	</div>
<center><button class="btn btn-lg btn-success" onclick="generateConfig()">Сохранить</button></center><br>
<script type="text/javascript" src="js/install.js"></script>
';

$nav = new nav();
$topmenu = $nav->showTopMenu($page,$version);

$main_site = new parser("template.tpl");
$main_site->get_tpl();
$main_site->set_tpl("%content%",$style);
$main_site->set_tpl("%topmenu%", $topmenu);
$main_site->set_tpl("%cheza%", $content);
$main_site->set_tpl("%left_menu%", $leftmenu);
print $main_site->tpl_parse();

?>
