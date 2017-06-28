<?php
/**
 * エジソンさんの中継サーバー情報を扱うクラス
 * @author Yuji Hamada
 * @since 2017/6/02
 * @version 2.0
 */
class RelayServer{

	public $id;
	public $company_id;
	public $relay_server_url;
	public $zensu_url;
	public $is_production;
	public $com_id;
	public $created;
	public $updated;
	
	/**
	 * サブグループIDから中継サーバー情報を取得
	 * @param string $geographicId
	 */
	public function getByGeographicId($geographicId){
		try{
			
			$dbh=SingletonPDO::connect();
			
			$sql = "
					SELECT
					    *
					FROM
					    relay_servers
					WHERE
					    id = (
					        SELECT
					            relay_server_id
					        FROM
					            geographic
					        WHERE
					            id = :geographic_id
					    )
					";
				
			$stmt=$dbh->prepare($sql);
				
			$param = array(
					'geographic_id' => $geographicId
			);
			$stmt->execute($param);
		
			$relay_server = $stmt->fetch(PDO::FETCH_ASSOC);
				
			return $relay_server;
				
		}catch(PDOException $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
	
	/**
	 * 全中継サーバー情報を取得（中継サーバーのURLがあるものという意味）
	 */
	public function getAll() {
		try{
		
			$dbh=SingletonPDO::connect();
		
			$sql="SELECT
					    *
					FROM
					    relay_servers
					WHERE
					    relay_server_url IS NOT NULL";
			
			$stmt=$dbh->prepare($sql);
			$stmt->execute();
			
			$servers=$stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $servers;
		}catch(Exception $e){
			echo $e->getMessage();
			error_log($e->getFile() . ':' . $e->getLine() . ' '. $e->getMessage() . ' ' . $e->getTraceAsString());
		}
	}
}