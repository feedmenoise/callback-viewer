<?php 
ob_start("ob_gzhandler");
session_start();
ini_set('display_errors', 0);

$version = "1.3";

if (!file_exists('./inc/config.php')) { error_log("popal v if"); $fp = fopen('./inc/config.php', 'w') or die("ne mogu sozdat'"); fclose($fp); }

include_once "./inc/config.php";
require_once "./inc/config-static.php";
require_once "./inc/ami.class.php";
require_once "./inc/dongles.class.php";
require_once "./inc/parser.class.php";
require_once "./inc/nav.class.php";
require_once "./inc/cdr.class.php";
require_once "./inc/message.class.php";

$remoteIP = $_SERVER['REMOTE_ADDR'];
$time = date("Y-m-d H:i:s"); 
$style = "<div class=\"container rtfy\">%cheza%</div>";
$leftmenu ="";
$page="cdr";

mysqli_report(MYSQLI_REPORT_STRICT); 

try {
	$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
} catch (mysqli_sql_exception $e) {
	header("Location: http://$_SERVER[HTTP_HOST]". LINK ."install.php");
	
}

if (isset($_GET["p"])) {
	$page    = $_GET["p"];
	$subpage = (isset($_GET["sp"])?$_GET["sp"]:"");
} 
else {
	header("Location: http://$_SERVER[HTTP_HOST]". LINK ."index.php?p=cdr");
}



switch($page) {
	case "cdr":
	$t = new cdr();
	$lm = new nav();
	$style = "<div class=\"container-fluid rtfy\"><div class=\"row\">%left_menu%<div class=\"col-md-auto table100_responsive\" >%cheza%</div></div>";

	if (isset($_GET["dep"])) {
		if (isset($_POST['search']) && $_POST['search'] != "") 
			$content = $t->tableView($_GET["dep"],$_POST["search"]);
		else
			$content = $t->tableView($_GET["dep"],"");	

		$leftmenu = $lm->showLeftMenu($_GET["dep"]);
	}
	else {
		if (isset($_POST['search']) && $_POST['search'] != "")
			$content = $t->tableView("",$_POST["search"]);
		else 
			$content = $t->tableView("","");
		$leftmenu = $lm->showLeftMenu("all");
	}

	break;

	case "dongles":
	#$dongles = array ("MTS-SUP","MTS-CORP","VODAFONE1","VODAFONE2","VODAFONE3","VDF4-OUT","KYIVSTAR1","KYIVSTAR2","LIFE","GGT-CORP", "VODAFONE-CORPORATE-SALES");
	$ami = new ami();
	$dongles = $ami->getDonglesList();
	#var_dump($dongles);
	$d = new dongles();	
	$content = $d->makeDongleTable($dongles, $ami);
	break;


	case "message":
	$m = new message();
	if (isset($_GET["s"])) {
		$content = $m->showContent($_GET["s"]);
	}
	else {
		$content = $m->showContent("");
	}
	break;

}

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
