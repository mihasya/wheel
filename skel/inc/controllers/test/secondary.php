<?php
class test_secondaryController extends wheelController {
    var $__layout = 'test_default';
    public function index() {
        return $this->_forward($this, 'singASong');
    }
    public function singASong() {
        $this->_pageTitle = 'wheel secondary test controller';
        $this->song = 'the song remains the same';
    }
}
?>