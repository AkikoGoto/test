<?php
require '../inc/dbconfig.php';
require '../class/data.class.php';
require '../class/SingletonPDO.class.php';
require '../class/branch.class.php';

 
class branchTests extends PHPUnit_Framework_TestCase
{
    private $branch;
 
    protected function setUp()
    {
     //   $this->branch = new Branch();
    }
 
    protected function tearDown()
    {
     //   $this->branch = NULL;
    }
 
    //サンプル
    public function testHogehoge()
    {
    	$result = Branch::hogehoge();
    	$this->assertEquals("hogehoge", $result);
    }
    
    public function testGetBranchIdByManagerId()
    {
        $result = Branch::getBranchIdByManagerId(2);
        $this->assertEquals(1, $result);
    }
 

    
}