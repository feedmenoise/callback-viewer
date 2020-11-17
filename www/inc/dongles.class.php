<?php 

class dongles {

	function makeDongleTable ($donglesArr, $ami) {

		$out = '
		<div class="row">
		<div class="col-12 col-md-6">';
		
		$array_size = count($donglesArr);
		$table_sepparator = ceil($array_size / 2);

		for ($i = 0; $i < $array_size; $i++ ) {
			$donglename = $donglesArr[$i];

			if ($i == $table_sepparator) $out .= '</div><div class="col-12 col-md-6">';

			$dongleRead = $ami->sendcommand2asterRead("DongleShowDevices", $donglename);
			if (strpos($dongleRead[26], 'GSM') !== false || strpos($dongleRead[26], 'Not') !== false) $panelColor="alert-danger";
			else if (strpos($dongleRead[26], 'Dialing') !== false) $panelColor="alert-info";
			else $panelColor="alert-success";
			
			$out .= '
			<div class="card___box">
			<div class="card autocollapse card__box-item ">
			<button class="btn alert-link '.$panelColor.'" type="button" data-toggle="collapse" data-target="#'.$donglename.'" aria-expanded="true" aria-controls="collapseOne">
			          '.$donglename.'
			        </button>
			<div id="'.$donglename.'" class="collapse dongleContainer" aria-labelledby="headingOne">   
			
			';
			foreach ($dongleRead as $key => $line) {
				if (strpos($line, 'Device:') !== false) $out .= $line."<br>";
				if (strpos($line, 'Exten:') !== false) $out .= $line."<br>";
				if (strpos($line, 'State:') === 0 ) $out .= "<b>".$line."</b><br>";
				if (strpos($line, 'AudioState:') !== false) $out .= $line."<br>";
				if (strpos($line, 'DataState:') !== false) $out .= $line."<br>";
				if (strpos($line, 'Manufacturer:') !== false) $out .= $line."<br>";
				if (strpos($line, 'Model:') !== false) $out .= $line."<br>";
				if (strpos($line, 'Firmware:') !== false) $out .= $line."<br>";
				if (strpos($line, 'IMEIState:') !== false) $out .= $line."<br>";
				if (strpos($line, 'IMSIState:') !== false) $out .= $line."<br>";
				if (strpos($line, 'GSMRegistrationStatus:') !== false) $out .= $line."<br>";
				if (strpos($line, 'RSSI:') !== false) $out .= $line."<br>";
				if (strpos($line, 'ProviderName:') !== false) $out .= $line."<br>";
				if (strpos($line, 'SubscriberNumber:') !== false) $out .= $line."<br>";
			}

			$out .= '
			<button type="button" class="btn btn-danger" onclick="dongleReset(\''.$donglename.'\')">Перезагрузить</button>
			</div></div></div>
			';

		}
		$out .='</div></div>';
		return $out;
	}
}
?>