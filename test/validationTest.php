<?php
require '../inc/dbconfig.php';
require '../class/data.class.php';
require '../class/SingletonPDO.class.php';
require '../class/validate.class.php';
require '../language/language_ja.php';

class validationTest extends PHPUnit_Framework_TestCase{

	public function testValidateFormAreaValid(){
		$navi = array('name' => 'ナビエリア名称',
			'navi_message' =>'ナビメッセージ',
			'latitude' =>'37.382518719563365',
			'longitude' =>'140.95912173657166',
			'radius' =>'200'
		);

		$form_validate = new Validate();

		$errors = $form_validate->validate_form_area($navi);

		$this->assertEmpty($errors);

	}

	public function testValidateFormAreaEmpty(){
		$navi = array('name' => '',
			'navi_message' =>'',
			'latitude' =>'',
			'longitude' =>'',
			'radius' =>''
		);

		$form_validate = new Validate();

		$errors = $form_validate->validate_form_area($navi);

		$this->assertContains(NAVI_AREA_NAME.DATE_VERI_NO, $errors);
		$this->assertContains(RADIUS_FROM_CENTER.DATE_VERI_NO, $errors);
		$this->assertContains(NAVI_MESSAGE.DATE_VERI_NO, $errors);
		$this->assertContains(LATITUDE.DATE_VERI_NO, $errors);
		$this->assertContains(LONGITUDE.DATE_VERI_NO, $errors);

	}

	public function testValidateFormAreaWrongType(){
		$navi = array('name' => '123',
			'navi_message' =>'123',
			'latitude' =>'あいうえお',
			'longitude' =>'あいうえお',
			'radius' =>'あいう'
		);

		$form_validate = new Validate();

		$errors = $form_validate->validate_form_area($navi);

		$this->assertContains(RADIUS_FROM_CENTER.ERROR_INT, $errors);
		$this->assertContains(LATITUDE.ERROR_FLOAT, $errors);
		$this->assertContains(LONGITUDE.ERROR_FLOAT, $errors);

	}
	
}