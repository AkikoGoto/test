<?php
	if($_GET['company_id']){	
	$id=sanitizeGet($_GET['company_id']);
	}
	user_auth($u_id,$id);
	$uploaddir = "uploaded/komatsu_obstacles/".$u_id;
	$filename = $_GET["filename"];
	
	$fp = fopen($uploaddir."/".$filename,"r");
	$csvdata = array();
	$dataNum = 0;
		while($data = fgetcsv($fp)){
	        for($i=0;$i<count($data);$i++){            
	        	$csvdata[$dataNum][$i%6] = $data[$i];
		        	if($i%6 == 5){
		        		$dataNum++;	
		        	}
	            }
	        }
	$smarty->assign("csvdata",$csvdata);
	$smarty->assign("filename","komatsu_viewCSV.html");
	$smarty->display("template.html");