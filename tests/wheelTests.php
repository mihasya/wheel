<?php
require_once('PHPUnit/Framework.php');
require_once('../skel/inc/core/wheel.php');
require_once('../skel/inc/controllers/test/main.php');
require_once('../skel/conf/settings.test.php');
class wheelTests extends PHPUnit_Framework_TestCase {
    public function testPartial() {
        $out = wheel::partial('test_main', 'test1', array('var1'=>'this is only a test') );
        $this->assertTrue((bool)preg_match("/^this is only a test/", $out));
        $out = wheel::partial('test_main', 'test1', array('var1'=>'') );
        $this->assertTrue($out=='');
    }
    public function testDispatch() {
        $output = wheel::dispatch('test_main', 'index');
        $this->assertTrue((bool)preg_match("/^\<html\>/m", $output));
        $output = wheel::dispatch('test_main', 'test1');
        $this->assertTrue((bool)preg_match("/^\<html\>/m", $output));
        $output = wheel::dispatch('test_main', 'index', true);
        $this->assertTrue($output == 'wheel goes round');
    }
    public function testForwardToAnotherController() {
        $output = wheel::dispatch('test_main', 'forward_test');
        $this->assertTrue((bool)preg_match("/the song remains the same/m", $output));
    }
}
?>