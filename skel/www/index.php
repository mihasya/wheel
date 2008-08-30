<?php
/**
 * wheel framework main router file
 * $Id: index.php 11 2008-03-25 00:55:34Z mihasya $
 * @author mikhail panchenko
 * @link http://mihasya.com/code/wheel
 * @copyright Copyright 2008, mikhail panchenko
 * @license http://opensource.org/licenses/bsd-license.php
 * @package wheel
 */
//configuration here plz
$conf['local_inc_dir'] = '/home/mikhailp/hackery/wheel/trunk/skel/inc'; //set this in a local variable first since ked:: does not yet exist.
$conf['conf_dir'] = '/home/mikhailp/hackery/wheel/trunk/skel/conf';
$conf['smarty_dir'] = '/home/mikhailp/hackery/wheel/trunk/skel/inc/smarty';
$conf['smarty_lib_dir'] = 'smarty';
$conf['env'] = 'dev'; //change this var to get it from wherever; should be 'dev' or 'prod' in the end
//end of configuration
require_once($conf['local_inc_dir'].'/core/wheel.php');
wheel::$conf = $conf; //pass the conf to our singleton
set_exception_handler('wheelExceptionHdl'); //i can has pretty excepshn reportz?
//this is how real men route, Richard Crowley
$controller = ($_GET['c']!='') ? $_GET['c'] : 'main';
$action = ($_GET['a']!='') ? $_GET['a'] : 'index';
$partial = isset($_GET['partial']); //in case we're trying to get a partial view asynchronously
//THUNDERCATS, GOOOOOO
echo wheel::dispatch($controller, $action, $partial);
?>
