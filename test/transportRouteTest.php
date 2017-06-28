<?php
require '../inc/dbconfig.php';
require '../class/SingletonPDO.class.php';
require '../class/transport_route.class.php';

 
class transportRouteTests extends PHPUnit_Framework_TestCase
{
 
    protected function setUp()
    {

     //テストデータの内容を書いておく
     	$this->destination_id = 3;
     	$this->name = '鶴屋町ルート';
     	$this->destination_name = 'かりおきば';
    }
 
    protected function tearDown()
    {
     //   静的なクラスメソッドしかテストしない場合は、特に書かなくてよい
    }
     
    public function testGetTransportRouteByDestination()
    {
    	$result = TransportRoute::getTransportRoutesByDestination($this->destination_id);
    	
    	$this->assertEquals($this->name, $result[0]['name']);
    	$this->assertEquals($this->destination_name, $result[0]['destination_name']);
    }
 

    
}