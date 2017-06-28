<?php
$shortest_keys = array( 'lat', 'lng' );
$roots = array();

if($_POST['root']) {
	
	foreach ($_POST['root'] as $destination ) {
		$destination_datas = array();
		foreach ( $shortest_keys as $key => $value ){

			$destination_datas[$value] = sanitizeGet( $destination[$value] );
		}
		array_push( $roots, $destination_datas );
	}
	
	$coordinates = array();
	foreach ( $roots as $root ) {
		
		$coordinates[] = $root['lat'] .','. $root['lng'];
		
	}
	
	$get_parameter = implode( "&point=", $coordinates );
	$root_api = SEARCH_ROOT_API.'point='.$get_parameter.'&instructions=false&points_encoded=false';
	//echo file_get_contents('http://192.168.1.101:8989/route??point=35.671989,139.763965&point=35.630152,139.74044&point=35.658774,139.701345&point=35.691161,139.700269&point=35.686014,139.730667&point=35.685441,139.752756&point=35.713759,139.777238&point=35.727772,139.770987&point=35.733627,139.739313&point=35.681683,139.766116&point=35.671989,139.763965');
	
	$roots = file_get_contents($root_api);
	
	header('Content-Type: application/json');
	echo $roots;
	
} else {
	echo 'null';
	return;
}