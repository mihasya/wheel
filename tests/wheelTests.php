<?php
require_once('PHPUnit/Framework.php');
require_once('../skel/inc/core/wheel.php');
require_once('../skel/inc/controllers/test/main.php');
require_once('../skel/conf/settings.test.php');
class wheelTests extends PHPUnit_Framework_TestCase {
    public function testPartial() {
        $out = wheel::partial('test_main', 'test1', array('var1'=>'this is only a test') );
        $match = preg_match("/^this is only a test/", $out);
        $this->assertTrue((bool)$match);
        $match = $out = null;
        $out = wheel::partial('internal_demo', 'nameCaller', array('var1'=>'') );
        $this->assertTrue($match=="");
    }
    public function testDispatch() {
        //TODO: create test controller/views with basic dom trees
    }
}
?>