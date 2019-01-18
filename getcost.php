<?php
error_reporting(0);
define('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter');
define('RAJAONGKIR_KEY', 'b07fae510ff4ee746acb585532dfd79f');

		
$cityid = $_POST['cityid'];
//$cityid = 153;
$provinceid = $_POST['provinceid'];
//$provinceid = 6;
$cityid2 = $_POST['cityid2'];
//$cityid2 = 151;
$provinceid2 = $_POST['provinceid2'];
//$provinceid2 = 6;
$kurir = $_POST['kurir'];
//$kurir = "tiki";
$weight = $_POST['weight'];
//$weight = 1;

if (empty($provinceid) || empty($cityid) || empty($provinceid2) || empty($cityid2) || empty($kurir)) {
	exit('No access allowed');
}

$result = array();
$status = 0;
$msg = 'Error Get Cost';
$returnhtml = "";
$weight = intval($weight * 1000);

$rjUrlCost = RAJAONGKIR_URL."/cost";

$ch = curl_init();
curl_setopt_array($ch, array(
  CURLOPT_URL => $rjUrlCost,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_POSTFIELDS => "origin=".$cityid."&destination=".$cityid2."&weight=".$weight."&courier=".$kurir."",
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
	"content-type: application/x-www-form-urlencoded",
	"key: ".RAJAONGKIR_KEY.""
  ),
));
$response = json_decode(curl_exec($ch), TRUE);
$err = curl_error($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$dataCostSend = array();
if($httpcode == 200){
	$rjStatus = isset($response['rajaongkir']['status']['code'])?$response['rajaongkir']['status']['code']:"";

	if($rjStatus == 200){
		$dataCostSend = isset($response['rajaongkir']['results'][0]['costs'])?$response['rajaongkir']['results'][0]['costs']:array();
		
		/* echo "<pre>";
		print_r($dataCostSend);
		echo "</pre>"; */
		
		if(count($dataCostSend) > 0){
			$returnhtml .= "<br><strong>";
			foreach($dataCostSend as $cost){
				$returnhtml .= $cost['service']." - Rp ".number_format($cost['cost'][0]['value'], 0, ',', '.')."<br>";
			}
			$returnhtml .= "</strong>";
			
			$result['countCost'] = count($dataCostSend);
		} else {
			$result['countCost'] = 0;
		}
		
		$status = 1;
	}
}
$result['status'] = $status;
$result['dataCost'] = $returnhtml;

echo json_encode($result);
?>