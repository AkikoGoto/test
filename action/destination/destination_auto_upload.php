<?php
$keys = array(
	"address", "category", "company_id", "contact_person", "department",
	"destination_kana", "destination_name", "email", "fax", "information",
	"latitude", "longitude", "postal", "tel" );
foreach ( $_POST['destinations'] as $num => $post_data ) {
	
	foreach ( $keys as $key) {
		
		$datas[$num][$key] = htmlentities($post_data[$key], ENT_QUOTES, mb_internal_encoding());
		
	}
	
}

try {

	if (!empty($datas)) {
		
		$status = Destination::putInDestinationsUploadedByCSV( $datas );
		
	} else {
		$status = "NO_DATA";
	}

} catch (Exception $e) {

	/* エラーメッセージをセット */
	$msg = array('red', $e->getMessage());

}

$result = array("status" => $status);
$json_result = json_encode($result);
header('Content-Type: application/json');
echo $json_result;
