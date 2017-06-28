<?php
//住所情報を、緯度経度に変換　一斉データ投入の際に利用

require_once('admin_check_session.php');

	if($_SESSION['address_id']){
		$address_id = $_SESSION['address_id']+1;	
		
	}else{
		$address_id =1;
	}

	$dbh=SingletonPDO::connect();
	$sql="	SELECT 
				address
			FROM mass_address
			WHERE id = $address_id";
	
	
	$stmt=$dbh->prepare($sql);
	$stmt->execute();

	while($res=$stmt->fetchAll()){
	
	$addresses[]=$res;
		}
	
//		var_dump($addresses);
	
	$address_for_geocodes=$addresses[0];	
		
	foreach($address_for_geocodes as $address_for_geocode){
	//	print $address_for_geocode['address'];
		$address=$address_for_geocode['address'];
		$_SESSION['address_id']=$address_for_geocode['id'];
		header("Location: index.php?action=geocode_mass_data&address=$address");
	}	

?>