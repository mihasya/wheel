<?php
/**
 * wheel framework sample mainController
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
class mainController extends wheelController {
  function index() {
    return $this->_forward('internal_demo', 'index');
  }
}
?>
