<?php
$shortest_keys = array( 'name', 'lat', 'lng' );
$destinations = array();

if($_POST['root_detail']) {
	
	foreach ($_POST['root_detail'] as $destination ) {
		$destination_datas = array();
		foreach ( $shortest_keys as $key => $value ){

			$destination_datas[$value] = sanitizeGet( $destination[$value] );
		}
		array_push( $destinations, $destination_datas );
	}
	
} else {
	
	echo 'null';
	return;
	
}


//$destinations = array (
//			array('name' => '銀座', "lat" => "35.671989","lng" => "139.763965"),
//			array( "name" => "渋谷","lat" => "35.658774","lng" => "139.701345"), 
//			array( "name" => "品川","lat" => "35.630152","lng" => "139.74044"), 
//			array( "name" => "渋谷","lat" => "35.658774","lng" => "139.701345"), 
//			array( "name" => "新宿","lat" => "35.691161","lng" => "139.700269"), 
//			array( "name" => "四ッ谷","lat" => "35.686014","lng" => "139.730667")
//			);


$data_string = json_encode( $destinations );

$ch = curl_init(SEARCH_SHORTEST_ROOT_API);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);                                                 
 
$result = curl_exec($ch);
curl_close($ch);

$result_decoded = json_decode($result);

if (is_array($result_decoded)) {
	echo $result;
} else {
	$fail_return['status'] = "CANNOT_SEARCH_ROUTE";
	$fail_return['origin_route'] = $destinations;
	$fail_return['error'] = $result_decoded;
	$fail_return_encode = json_encode( $fail_return );
	echo $fail_return_encode;
}

return;




$result_decoded = json_decode($result);

$coordinates = array();
foreach ( $result_decoded as $destination ) {
	
	$coordinates[] = $destination->lat .','. $destination->lng;
	
}

$get_parameter = implode( "&point=", $coordinates );
$root_api = SEARCH_ROOT_API.'?point='.$get_parameter.'&instructions=false&points_encoded=false';
//echo file_get_contents('http://192.168.1.101:8989/route??point=35.671989,139.763965&point=35.630152,139.74044&point=35.658774,139.701345&point=35.691161,139.700269&point=35.686014,139.730667&point=35.685441,139.752756&point=35.713759,139.777238&point=35.727772,139.770987&point=35.733627,139.739313&point=35.681683,139.766116&point=35.671989,139.763965');
$roots = file_get_contents($root_api);

header('Content-Type: application/json');
echo $roots;


