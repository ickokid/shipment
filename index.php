<?php
define('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter');
define('RAJAONGKIR_KEY', 'b07fae510ff4ee746acb585532dfd79f');

$rjUrlProvince = RAJAONGKIR_URL."/province";
		
$ch = curl_init();
curl_setopt_array($ch, array(
  CURLOPT_URL => $rjUrlProvince,
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

$dataProvinceSend = array();
if($httpcode == 200){
	$rjStatus = isset($response['rajaongkir']['status']['code'])?$response['rajaongkir']['status']['code']:"";

	if($rjStatus == 200){
		$dataProvinceSend = isset($response['rajaongkir']['results'])?$response['rajaongkir']['results']:array();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Shipment</title>
</head>
<body>
<fieldset>
	<legend>Pengirim:</legend>
	Propinsi : 
	<select name="province" id="IDprovince">
		<option value="0">-- Pilih Propinsi --</option>
		<?php foreach($dataProvinceSend as $id => $valSend){ ?>
		<option value="<?php echo $valSend['province_id']; ?>"><?php echo $valSend['province']; ?></option>
		<?php } ?>
	</select><br>
	Kota : 
	<select name="city" id="IDcity">
		<option value="0">-- Pilih Kota --</option>
	</select>
</fieldset>

<fieldset>
<legend>Penerima:</legend>
Propinsi : 
<select name="province2" id="IDprovince2">
	<option value="0">-- Pilih Propinsi --</option>
	<?php foreach($dataProvinceSend as $id => $valSend){ ?>
	<option value="<?php echo $valSend['province_id']; ?>"><?php echo $valSend['province']; ?></option>
	<?php } ?>
</select><br>
Kota : 
<select name="city2" id="IDcity2">
	<option value="0">-- Pilih Kota --</option>
</select>
</fieldset><br>

Weight (kg) : <input type="text" name="weight" id="IDweight"><br>
Kurir : <select name="kurir" id="IDkurir">
	<option value="jne">JNE</option>
	<option value="pos">POS</option>
	<option value="tiki">TIKI</option>
</select>

<br><br>

<input type="button" name="btnCheck" id="IDbtnCheck" value="Hitung Cost">
<div id="IDresultCost">

</div>

<script src="js/jquery-3.2.0.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#IDprovince').change(function(){ 
			var provinceid = $('#IDprovince').val();
			
			var params = "provinceid="+provinceid;
			
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "getcity.php",
				data: params,
				error: function( xhr, tStatus, err ) {
					alert("e :" + tStatus + " " + err);
				},
				success: function(data) //we're calling the response json array 'cities'
				{
					if(data.status == 1){
						if(data.countCity > 0){
							$("#IDcity").html(data.dataCity);
						} 
					} 
				} 
			 });
			
			return false;
		});
		
		
		$('#IDprovince2').change(function(){ 
			var provinceid = $('#IDprovince2').val();
			
			var params = "provinceid="+provinceid;

			$.ajax({
				type: "POST",
				dataType: "json",
				url: "getcity.php",
				data: params,
				error: function( xhr, tStatus, err ) {
					alert("e :" + tStatus + " " + err);
				},
				success: function(data) //we're calling the response json array 'cities'
				{
					if(data.status == 1){
						if(data.countCity > 0){
							$("#IDcity2").html(data.dataCity);
						} 
					} 
				} 
			 });
			
			return false;
		});
		
		
		$("#IDbtnCheck").click(function(){
			var provinceid 		= ""; 
			var cityid 			= ""; 
			var provinceid2 	= ""; 
			var cityid2 		= ""; 
			var kurir 			= ""; 
			var weight 			= ""; 
			
			provinceid 		= $("#IDprovince").val();
			cityid 			= $("#IDcity").val();
			provinceid2 	= $("#IDprovince2").val();
			cityid2 		= $("#IDcity2").val();
			kurir 			= $("#IDkurir").val();
			weight 			= $("#IDweight").val();
			
			var params = "provinceid="+provinceid+"&cityid="+cityid+"&provinceid2="+provinceid2+"&cityid2="+cityid2+"&kurir="+kurir+"&weight="+weight;
			
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "getcost.php",
				data: params,
				error: function( xhr, tStatus, err ) {
					alert("e :" + tStatus + " " + err);
				},
				success: function(data){
					if(data.status == 1){
						$("#IDresultCost").html(data.dataCost);
					} 
				}
			});
			
			return false;
		});
	});
</script>
</body>
</html>