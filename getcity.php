<?php
error_reporting(0);
define('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter');
define('RAJAONGKIR_KEY', 'b07fae510ff4ee746acb585532dfd79f');
	
$provinceid = $_POST['provinceid'];
//$provinceid = 1;

if (empty($provinceid)) {
	exit('No access allowed');
}

$result = array();
$status = 0;
$msg = 'Error Get City';
$returnhtml = "";

$rjUrlCity = RAJAONGKIR_URL."/city?province=".$provinceid."";

$ch = curl_init();
curl_setopt_array($ch, array(
  CURLOPT_URL => $rjUrlCity,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
	"key: ".RAJAONGKIR_KEY.""
  ),
));
$response = json_decode(curl_exec($ch), TRUE);
$err = curl_error($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$dataCitySend = array();
if($httpcode == 200){
	$rjStatus = isset($response['rajaongkir']['status']['code'])?$response['rajaongkir']['status']['code']:"";

	if($rjStatus == 200){
		$dataCitySend = isset($response['rajaongkir']['results'])?$response['rajaongkir']['results']:array();
		
		if(count($dataCitySend) > 0){
			$returnhtml .= "<option value='0'>-- Pilih Kota --</option>";
			foreach($dataCitySend as $city){
				$returnhtml .= "<option value='".$city['city_id']."'>".$city['city_name']."</option>";
			}
			
			$result['countCity'] = count($dataCitySend);
		} else {
			$result['countCity'] = 0;
		}
		
		$status = 1;
	}
}
$result['status'] = $status;
$result['dataCity'] = $returnhtml;

echo json_encode($result);
?>