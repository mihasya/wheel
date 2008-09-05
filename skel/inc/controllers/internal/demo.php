<?php
/**
 * wheel framework demo controller
 * $Id: ked.php 10 2008-03-25 00:39:09Z mihasya $
 * @author mikhail panchenko
 * @link http://mihasya.com/code/wheel
 * @copyright Copyright 2008, mikhail panchenko
 * @license http://opensource.org/licenses/bsd-license.php
 * @package wheel
 */
/**
 * a basic controller
 * @package wheel
 */
class internal_demoController extends wheelController {
  var $__layout = 'internal_wheel';
  function index() {
    $this->passAlongTest = 'Variable passed along in the forward!';
    return $this->_forward($this, 'action1');
  }
  function action1() {
    return $this->_forward($this, 'nameCaller');
  }
  function nameCaller() {
    $this->_pageTitle = 'wheel tester: name caller';
    $name = $_GET['name'];
    $this->name = ($name=='') ? 'Ned' : ucfirst($name);
  }
}
?>
