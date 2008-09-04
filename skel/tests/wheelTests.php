<?php
require_once('PHPUnit/Framework.php');
require_once('../inc/core/wheel.php');
require_once('../inc/controllers/internal/demo.php');
require_once('../conf/settings.test.php');
class wheelTests extends PHPUnit_Framework_TestCase {
    public function testPartial() {
        $ctl = wheel::inst('internal_demo');
        $out = wheel::partial('internal_demo', 'nameCaller', array('name'=>'Mike') );
        $match = preg_match("/Mike!/", $out);
        $this->assertTrue((bool)$match);
        $out = wheel::partial('internal_demo', 'nameCaller', array('name'=>'') );
        $match = preg_match("/Hello, !/", $out);
        $this->assertTrue((bool)$match);
    }
}
?>