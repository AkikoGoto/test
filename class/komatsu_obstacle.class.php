<?php
class Komatsu_obstacle{
	
	//ディレクトリ取得
	//ディレクトリが存在するかチェックし、なければ作成
	public function get_uploaded_directory($u_id){
		$uploaddir = "uploaded/komatsu_obstacles/".$u_id;
		if(!is_dir($uploaddir)){
		umask(0);
		mkdir($uploaddir, 0777);
		}
		return $uploaddir;
	}
	
	public function is_file_exists($u_id, $file_name) {
		$base_dir = $this->get_uploaded_directory($u_id);
		return file_exists($base_dir . "/" . $file_name);
	}
	
	//ファイル名取得
	public function get_file_info($uploaddir){
		$res_dir = opendir($uploaddir);
		$csvMetaData = array();
		$date_for_sort = array();
		$count = 0;
		
		while( $file_name = readdir( $res_dir ) ){
			if($file_name !== "." && $file_name !== ".."){
			$csvMetaData[$count]["filename"]=$file_name;
			$csvMetaData[$count]["uploadTime"]= date ("Y/m/d  H:i:s", filemtime($uploaddir."/".$file_name));
			$date_for_sort[] = date ("Y/m/d  H:i:s", filemtime($uploaddir."/".$file_name));
			$count++;
			}
		}
		closedir( $res_dir );
		array_multisort($date_for_sort, SORT_DESC, $csvMetaData);
		return $csvMetaData;
	}
	
	public function delete_file($filename,$uploaddir){
		$file_pass = $uploaddir."/".$_SESSION['komatsu_obstacle']['filename'];
		
		if ( file_exists( $file_pass )) {
			if(unlink($file_pass)){
				$msg = $_SESSION['komatsu_obstacle']['filename']."を削除しました。";
			}
		} else {
			$msg = "そのファイルは存在しません。";
		}
		return $msg;
	}
	
	public function get_file_time($company_id, $file_name) {
		$path = $this->get_uploaded_directory($company_id)."/".$file_name;
		if(file_exists($path)) {
			return filemtime($path);
		}
		return false;
	}
	
	public function getObstacleJSONByDriver($company_id, $driver_id) {
		$uploaddir = Komatsu_obstacle::get_uploaded_directory($company_id);
		$filename = Komatsu_obstacle::getFilenameByDriver($company_id, $driver_id);
		
		$fp = fopen($uploaddir."/".$filename,"r");
		$data = array();
		$dataNum = 0;
		while($row = fgetcsv($fp)){
			$data[] = array(
				'type' => $row[1],
				'latitude' => $row[2],
				'longitude' => $row[3],
				'heading' => $row[4],
				'headingFilter' => $row[5]
			);
		}
		return $data;
	}
	
	public function getFilenameByDriver($company_id, $driver_id) {
		$filename = false;
		try{
			$dbh=SingletonPDO::connect();
		
			$sql=<<<EOL
SELECT
	obstacles_file_name
FROM komatsu_driver_options
WHERE driver_id = $driver_id
LIMIT 1
EOL;
		
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			$filename=$stmt->fetchColumn();
			
		} catch(Exception $e) {
	
			echo $e->getMessage();
	
		}
		return $filename;
	}
}