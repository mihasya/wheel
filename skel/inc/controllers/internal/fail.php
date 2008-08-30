<?php
/**
 * exception handling controller for wheel
 * $Id: fail.php 10 2008-03-25 00:39:09Z mihasya $
 * @author mikhail panchenko
 * @link http://mihasya.com/code/wheel
 * @copyright Copyright 2008, mikhail panchenko
 * @license http://opensource.org/licenses/bsd-license.php
 * @package wheel
 */
 /**
 * a basic controller for error/exception handling
 * @package wheel
 */
class internal_failController extends wheelController {
  /**
   * puke the trace
   */
  function puke () {
    $this->_layout='internal_wheel';
    $this->pageTitle = 'wheelvc error report';
  }
}
?>
