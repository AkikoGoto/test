<?php
require '../inc/dbconfig.php';
require '../class/data.class.php';
require '../class/SingletonPDO.class.php';
require '../class/communication.class.php';
require '../language/language_ja.php';

 
class communicationTest extends PHPUnit_Framework_TestCase
{
	public function testTransportStartErrorMessage(){
		$message = Communication::transportErrorMessage(01);
		$this->assertEquals(TRANSPORT_START_ERROR_MESSAGE, $message);
	}
	public function testTransportEndErrorMessage(){
		$message = Communication::transportErrorMessage(13);
		$this->assertEquals(TRANSPORT_END_ERROR_MESSAGE, $message);
	}

}