<?php
error_reporting(0);

$shipment = "jne";
$resi = "540970006535019";

if(!empty($resi)){
	$result = extracthtml_aftership($shipment,$resi);
	
	echo "No Resi ".$shipment." : ".$resi."<br>";
	echo "<pre>";
	print_r($result);
	echo "</pre>";
} else {
	echo "exit";
}



function extracthtml_aftership($shipment="jne", $resi) {
	$arrTracking = array();
	
	if($shipment == "jne"){
		$base = "https://track.aftership.com/jne/".$resi."?";
	} else {
		$base = "https://track.aftership.com/pos-indonesia/".$resi."?";
	}
	
	$user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:64.0) Gecko/20100101 Firefox/64.0";
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);
	
	if($shipment == "jne"){
		$start = "<li class=\"checkpoint\">";
		$end   = "</ul></div></div></div></div><div class=\"row\">";
		$startPosisition = strpos($str, $start);
		$endPosisition   = strpos($str, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result = substr($str, $startPosisition, $longText);
		
		$str = trim($result);
		$search = array("'Indonesia'");
		$replace = array("");
		$str = preg_replace($search,$replace,$str);

		$arrStr = explode('<li class="checkpoint">',$str);
		
		$idx = 0;
		if(count($arrStr) > 0){
			foreach($arrStr as $val){
				if(!empty($val)){
					$search = array("'</li>'");
					$replace = array("");
					$val = preg_replace($search,$replace,$val);
	
					$val = str_replace(array('<div class="hint"></div>','<div class="checkpoint__icon delivered"></div>','<div class="checkpoint__icon intransit"></div>','<strong>','</strong>'),'',$val);
					
					$actJNE = preg_replace('/<div class=\"checkpoint__time\">([^`]*?)<\/div>/', '', $val);
					$actJNE = strip_tags($actJNE);
					$actJNE = substr($actJNE, 0, -3);
					
					$dtJNE = preg_replace('/<div class=\"checkpoint__content\">([^`]*?)<\/div>/', '', $val);
					$dtJNE = strip_tags(str_replace('<div class="hint">',' ',$dtJNE));
					
					$arrTracking[$idx]['jne_time'] = date("d F Y H:i:s", strtotime($dtJNE));
					$arrTracking[$idx]['jne_action'] = $actJNE;
					
					$idx++;
				}
			}
		}
	} else {
		$start = "<li class=\"checkpoint\">";
		$end   = "</ul></div></div></div></div><div class=\"row\">";
		$startPosisition = strpos($str, $start);
		$endPosisition   = strpos($str, $end); 
		
		$longText = $endPosisition - $startPosisition;
		$result = substr($str, $startPosisition, $longText);
		
		$str = trim($result);
		$search = array("'Pos Indonesia Domestic'");
		$replace = array("");
		$str = preg_replace($search,$replace,$str);

		$arrStr = explode('<li class="checkpoint">',$str);
		
		$idx = 0;
		if(count($arrStr) > 0){
			foreach($arrStr as $val){
				if(!empty($val)){
					$search = array("'</li>'");
					$replace = array("");
					$val = preg_replace($search,$replace,$val);
					
					$val = str_replace(array('<div class="hint"></div>','<div class="checkpoint__icon delivered"></div>','<div class="checkpoint__icon intransit"></div>','<strong>','</strong>'),'',$val);
					
					$actJNE = preg_replace('/<div class=\"checkpoint__time\">([^`]*?)<\/div>/', '', $val);
					$actJNE = preg_replace('/<div class=\"hint\">([^`]*?)<\/div>/', '', $actJNE);
					$actJNE = str_replace(array('<span class="checkpoint__courier-name">'),' ',$actJNE);
					$actJNE = strip_tags($actJNE);
					
					$dtJNE = preg_replace('/<div class=\"checkpoint__content\">([^`]*?)<\/div>/', '', $val);
					$dtJNE = strip_tags(str_replace('<div class="hint">',' ',$dtJNE));
					
					$arrTracking[$idx]['jne_time'] = date("d F Y H:i:s", strtotime($dtJNE));
					$arrTracking[$idx]['jne_action'] = $actJNE;
					
					$idx++;
				}
			}
		}
	}
	
	return $arrTracking;
}
?>