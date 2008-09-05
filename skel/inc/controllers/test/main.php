<?php
class test_mainController extends wheelController {
    var $__layout = 'test_default';
    public function index() {
        return $this->_forward($this, 'test1');
    }
    public function test1() {
        $this->_pageTitle = 'wheel test';
        $this->var1 = 'wheel goes round';
    }
    public function forward_test() {
        return $this->_forward('test_secondary', 'singASong');
    }
}
?>