<?php

class getData extends Data{

	
	//全会社データ取得
		
	public function getAllCompanyData(){
	try{
	
		$dbh=SingletonPDO::connect();
		
		$sql="SELECT * from company ORDER BY company_name";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
	
		while($res=$stmt->fetch(PDO::FETCH_ASSOC)){
		$prefecture[]=$res;
			}
			
		return $prefecture;	
		exit;
		
		}catch(Exception $e){
		echo $e->getMessage();
			}
		}

	//住所から検索機能の県名取得
		
	public function getPrefectureData(){
	try{
	
		$dbh=SingletonPDO::connect();
		
		$sql="SELECT DISTINCT prefecture from geographic ORDER BY prefecture";
		
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
	
		while($res=$stmt->fetch(PDO::FETCH_NUM)){
		$prefecture[]=$res;
			}
			
		return $prefecture;	
		exit;
		
		}catch(Exception $e){
		echo $e->getMessage();
			}
		}
	
	//住所から検索機能の市名取得
		
	public function getCityData($prefecture){
	try{
	
		$dbh=SingletonPDO::connect();
		
		$sql="SELECT DISTINCT city from geographic where prefecture = $prefecture";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
	
		while($res=$stmt->fetch(PDO::FETCH_NUM)){
		$cities[]=$res;
			}
			
		return $cities;	
		exit;
		
		}catch(Exception $e){
		echo $e->getMessage();
			}
		}

	//住所から検索機能の区名取得
	public function getWardData($city){
		try{
	
		$dbh=SingletonPDO::connect();
			
		$sql="SELECT DISTINCT ward from geographic 
			  WHERE 
				ward NOT LIKE '' 
			  AND
				city='".$city."'
			";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
		while($res=$stmt->fetch(PDO::FETCH_NUM)){
			$wards[]=$res;
			}
		return $wards;	
		exit;
		
		}catch(Exception $e){
		echo $e->getMessage();
			}
	}	

	//市がない区を取得
		
	public function getNocityWardData($prefecture){
	try{
	
		$dbh=SingletonPDO::connect();
		
		$sql="SELECT 
				DISTINCT ward from geographic 
			  WHERE prefecture = $prefecture 							
			  AND city=''";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
	
		while($res=$stmt->fetch(PDO::FETCH_NUM)){
		$cities[]=$res;
			}
			
		return $cities;	
		exit;
		
		}catch(Exception $e){
		echo $e->getMessage();
			}
		}
		
		
	//区がない町村を取得
		
	public function getNoWardData($city){
	try{
	
		$dbh=SingletonPDO::connect();
		
		$sql="SELECT 
				DISTINCT town from geographic 
			  WHERE city='".$city."'
			  AND ward=''";
			
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
	
		while($res=$stmt->fetch(PDO::FETCH_NUM)){
		$towns[]=$res;
			}
			
		return $towns;	
		exit;
		
		}catch(Exception $e){
		echo $e->getMessage();
			}
		}

	//サービス別に取得
		
	public function searchByServiceData($refine_conditions){
	try{
	
		$dbh=SingletonPDO::connect();

		//県名だけはAND検索か、OR検索か選べない
		if($refine_conditions['by_prefecture']){
			$refine_pref='geographic.prefecture='.$refine_conditions['by_prefecture'];
		//	$in1='geographic IN ( SELECT * FROM geographic WHERE ';
		//	$in2=')';
		}		
		
		if($refine_conditions['business_hours_24']){
			$refine[]='company.business_hours_24=1';
		}
		
		if($refine_conditions['company']){
			$refine[]='company.is_company=0';
		}
		
		if($refine_conditions['individual']){
			$refine[]='company.is_company=1';
		}
		
		if($refine_conditions['credit']){
			$refine[]='company.credit=1';
		}
		
		if($refine_conditions['debit']){
			$refine[]='company.debit=1';
		}
		

		
		if($refine_conditions['services']){

			foreach($refine_conditions['services'] as $refine_condition_service){
				$refine[]="company_service.service_id =$refine_condition_service";
			}

			//$join_company_service="join company_service	on company.id = company_service.company_id";
			
		}
		
		$where ='WHERE ';
		
		if($refine){
			$and=' AND ';
			$bracket ='(';
			
				if($refine_conditions['and_or']){			
					$and_or=' '.$refine_conditions['and_or'].' ';
				}else{
					$and_or=' AND ';
				}
			$where2=')';
			
			//$refine_sql=implode(' AND ', $refine);	
			$refine_sql=implode($and_or, $refine);	
			
		}
		
		$data_max=DATA_MAX;

			$sql="
			SELECT 
				
				company.*,
				geographic.id as geographic_id,
				geographic.*,
				prefectures.id as prefectures_id,
				prefectures.*,
				company_service.id as service_company_id,
				company_service.*
				
			FROM company
			join geographic
				on company.id = geographic.company_id
			join prefectures
				ON prefectures.id=geographic.prefecture
			left join company_service	
				on company.id = company_service.company_id	
			$where $bracket $refine_sql $where2
			$and $refine_pref
			LIMIT $data_max
			";		
				
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	
		while($res=$stmt->fetchObject(__CLASS__)){
			$datas[]=$res;
			}

			
/*		for($i =0, $num_datas=count($datas); $i < $num_datas; $i++){

			$geo_id[$i]=$datas[$i]->geographic_id;
		}	

		if($geo_id){
			$geo_result=array_unique($geo_id);			
			$geo_id_refined=array_keys($geo_result);
	
			foreach($geo_id_refined as $each_geo_id_refined){
				
				$refined_datas[]=$datas[$each_geo_id_refined];
				
			}
		*/
		for($i =0, $num_datas=count($datas); $i < $num_datas; $i++){

			$company_id_result[$i]=$datas[$i]->company_id;
		}	

		if($company_id_result){
			$company_id_result_unique=array_unique($company_id_result);			
			$company_id_refined=array_keys($company_id_result_unique);
	
			foreach($company_id_refined as $each_company_id_refined){
				
				$refined_datas[]=$datas[$each_company_id_refined];
				
			}
			
		}else{
			
	//			$refined_datas[]=NULL;
			
		}
			
			
		return $refined_datas;
	
		}
		catch(Exception $e)
		{
		echo $e->getMessage();
			}
		}
			
	
}


?>