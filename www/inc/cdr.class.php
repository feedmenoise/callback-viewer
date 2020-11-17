<?php 
class cdr {

	function tableView ($dep,$search) {

		$out = "
		<div class=\"table100\">
		<table>
		<thead>
		<tr class=\"table100-head\">
		<th class=\"column9\">Фирма</th>
		<th class=\"column1\">Номер</th>
		<th class=\"column6\">Статус</th>
		<th class=\"column2\">Входящий</th>
		<th class=\"column3\">Отдел</th>
		<th class=\"column4\">Оператор</th>
		<th class=\"column5\">Обработан</th>
		<th class=\"column7\">Запись</th>
		</tr>
		</thead>
		<tbody>";

		if (isset($_GET['page']) && is_numeric($_GET['page']))
			$page = $_GET['page'];
		else
			$page = 1;

		$connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

		if ($search != "") {
			if ($dep != "") {
				$result = $connect->query("SELECT * FROM callbacklog WHERE queuename='$dep' AND callback_callid LIKE '%$search%' OR queuename='$dep' AND callid LIKE '%$search%' OR queuename='$dep' AND number LIKE '%$search%' OR queuename='$dep' AND agent LIKE '%$search%'");
			}
			else {
				$result = $connect->query("SELECT * FROM callbacklog WHERE callback_callid LIKE '%$search%' OR callid LIKE '%$search%' OR number LIKE '%$search%' OR agent LIKE '%$search%'");

			}
		}
		else {
			if ($dep != "") 
				$result = $connect->query("SELECT * FROM callbacklog WHERE queuename=\"$dep\"");
			else 
				$result = $connect->query("SELECT * FROM callbacklog ");
		}

		$quantity=50; 
		$limit=3;

		if ($page<1) 
			$page=1; 
		
		$num = $result->num_rows;
		$pages = $num/$quantity;
		$pages = ceil($pages);
		$pages++; 

		if ($page>$pages) 
			$page = 1;
		
		if (!isset($list)) 
			$list=0;
		
		$list=--$page*$quantity;

		if ($search != "") {
			if ($dep != "") {
				$result2 = $connect->query("SELECT * FROM callbacklog WHERE queuename='$dep' AND callback_callid LIKE '%$search%' OR queuename='$dep' AND callid LIKE '%$search%' OR queuename='$dep' AND number LIKE '%$search%' OR queuename='$dep' AND agent LIKE '%$search%' ORDER BY id DESC LIMIT $quantity OFFSET $list");
			}
			else {
				$result2 = $connect->query("SELECT * FROM callbacklog WHERE callback_callid LIKE '%$search%' OR callid LIKE '%$search%' OR number LIKE '%$search%' OR agent LIKE '%$search%' ORDER BY id DESC LIMIT $quantity OFFSET $list");

			}
		}
		else {
			if ($dep != "") 
				$result2 = $connect->query("SELECT * FROM callbacklog WHERE queuename='$dep' ORDER BY id DESC LIMIT $quantity OFFSET $list");
			else 
				$result2 = $connect->query("SELECT * FROM callbacklog ORDER BY id DESC LIMIT $quantity OFFSET $list");
		}


			#$result2 = $connect->query("SELECT * FROM callbacklog ORDER BY id DESC LIMIT $quantity OFFSET $list");
		$num_result = $result2->num_rows;

		while($row= mysqli_fetch_array($result2)) {

			$phone = $row['number'];
			$company = explode("-", $row['queuename']);
			if ($company[0] == "gl") $company_img="logo-gorodok-sm.png";
			else if ($company[0] == "ggt") $company_img="logo-gigabit-sm.png";
			else $company_img = "logo-gigabit-sm.png";

			#$phone_check = $this->parseInfo($phone,$this->getInfoByPhone($phone));
			#<td class=\"column1\"><div class=\"number\">".$phone."</div><p class=\"".$phone."\"></p>&nbsp;</td>

			$out .="
			<tr>
			<td class=\"column1\">
				<img class=\"cdr_company_img\" src=\"img\\".$company_img."\">
			</td>
			<td class=\"column2\">
			<div class=\"client\">
    			<div class=\"number\"><b>".$phone."</b></div>
    			<div class=\"login\"></div>
    			<div class=\"company\" style=\" display: none\">".$company[0]."</div>
    		</div>

			</td>
			<td class=\"column3\">";

			switch($row['flag']) {
					#unanswered
				case 1:
				$icon = "img/unanswered.png";
				$icon_desc = "Пропущен";
				break;

					#answered
				case 2:
				$icon = "img/ok.png";
				$icon_desc = "Обработан";
				break;

					#BUSY
				case 3:
				$icon = "img/busy.png";
				$icon_desc = "Занят";
				break;

					#NOANSWER
				case 4:
				$icon = "img/busy.png";
				$icon_desc = "Нет ответа";
				break;

					#CANCEL
				case 5:
				$icon = "img/later.png";
				$icon_desc = "Отклонен оператором";
				break;

					#HANGUP
				case 6:
				$icon = "img/later.png";
				$icon_desc = "Отклонен абонентом";
				break;

					#CHANUNAVAIL
				case 7:
				$icon = "img/busy.png";
				$icon_desc = "Не доступен";
				break;
				case 8:
				$icon = "img/ok.png";
				$icon_desc = "Дозвонился";
				break;
			}

			$out .= "<div class=\"status_container\" ><img src=\"".$icon."\" alt=\"".$icon_desc."\" class=\"status_icon\" ><div class=\"status_overlay\"><div class=\"status_text\">".$icon_desc."&nbsp;</div></div></div></td>
			<td class=\"column4\"> ".$row['time_1']." &nbsp;</td>
			<td class=\"column5\"> ".$row['queuename']." &nbsp;</td>
			<td class=\"column6\">
			<div class=\"peer\">
    			<div class=\"number\"><b>".$row['agent']."</b></div>
    			<div class=\"name\"></div>
    		</div></td>
			<td class=\"column7\">".$row['time_2'] ."&nbsp;</td>
			<td class=\"column8\">";

			if ($row['callback_callid']!=null)  {
				$link = $row['callid']; 
				$out .= "<button type=\"button\" class=\"btn btn-sm btn-info play-rec\" data-toggle=\"modal\" data-target=\"#exampleModalCenter\" data-id=\"".$row['id']."\"><span data-feather=\"play\"></span></button>";
			}

			$out .= "&nbsp;</td></tr>";
		}

		$out .= "</tbody></table></div>
		<ul class=\"pagination\">";

		if ($page>=1) {
			$out .= '<li><a href="' . $_SERVER['SCRIPT_NAME'] . '?p=cdr&page=1">«</a></li>';
		}
		$thisPage = $page+1;
		$start = $thisPage-$limit;
		$end = $thisPage+$limit;
		$depGet = "";
		if ($dep != "") $depGet="&dep=".$dep;
		for ($j = 1; $j<$pages; $j++) {
			if ($j>=$start && $j<=$end) {
				if ($j==($page+1))  $out .= '<li class="active"><a href="' . $_SERVER['SCRIPT_NAME'] . '?p=cdr'.$depGet.'&page=' . $j . '" >' . $j . '</a></li>';
				else $out .= '<li><a href="' . $_SERVER['SCRIPT_NAME'] . '?p=cdr'.$depGet.'&page=' . $j . '">' . $j . '</a></li>';
			}
		}
		if ($j>$page && ($page+2)<$j) {
			$out .= '<li><a href="' . $_SERVER['SCRIPT_NAME'] . '?p=cdr'.$depGet.'&page=' . ($j-1) . '">»</a></li>';
		}

		$out .="</ul>";

		return $out;
	}

}

?>
