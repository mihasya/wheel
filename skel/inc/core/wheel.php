<?php
/**
 * wheel framework main include
 * $Id: ked.php 10 2008-03-25 00:39:09Z mihasya $
 * @author mikhail panchenko
 * @link http://mihasya.com/code/wheel
 * @copyright Copyright 2008, mikhail panchenko
 * @license http://opensource.org/licenses/bsd-license.php
 * @package wheel
 */
/**
 * basic controller class for wheel vc framework
 * @property Array $_plugins array of plugins to be used by this controller
 * @property Array $_map associative array mapping 'method'=>'external function'
 * @property string $_layout determines what layout to use; 'default' initially, as in default.tpl
 * @property bool $_skipAssign determine whether or not _render skips smarty var assignment
 * @package wheel
 */
class wheelController {
    public  $_layout = 'default';
    public  $_view = '';
    private $_skipAssign = false;
    function __toString() {
        return 'Instance of ' . get_class($this);
    }
    /**
     * blank preExecute; if not overridden, nothing happens.
     * this can be used for things that you want executed before every action, like an auth function
     * can be used to call an _abort etc.
     */
    function _preExecute() { return; }
    /**
     * blank postExecute; if not overridden, nothing happens.
     * this can be used for things that you want executed after every action, like an auth function
     * can be used to call an _abort etc.
     */
    function _postExecute() { return; }
    /**
     * abort the execution of an action. This is different from forward_to in that it sets the _abort property
     * that the _execute function checks at the very top. this is useful for plugins that want to run a
     * function before the execution of every action, such as a per-controller auth plugin
     * @param string $controller the controller to forward to after the abort
     * @param string $action the action to execute after abort.
     */
    function _abort($controller, $action, $param=null) {
        $this->_abort = array('controller'=>$controller, 'action'=>$action, 'param'=>$param);
    }
    /**
     * execute the requested action from the controller
     * @param string $action the action to be executed
     * @param array $param the associative array of 'param'=>'value' array; replaced with $_REQUEST if left null
     * @return mixed $result the result of execution. could be an array to be interpreted by wheel::dispatch (see forward and redirect)
     */
    function _execute($action, $param=null) {
        $this->_preExecute();
        if ($this->_abort) return $this->_abort; //if somewhere before execution (in a plugin, for example) the abort flag was thrown, return that to the dispatch
        $this->_view = $action; //default _view value; can be overridden to null in the action to avoid rendering the .tpl
        if (!method_exists($this, $action)) {
            $this->_skipAssign = true; //tell _render to skip smarty->assign related steps for this.
            return; //return nothing;
        }
        $result = call_user_method($action, $this); //execute the method if it exists.
        $this->_postExecute();
        return $result;
    }
    /**
     * render the appropriate smarty template for this action. render in this case means set $this->viewContent
     * @param string $action which action is to be renered
     */
    function _render($action) {
        if ($this->_view===null) return; //if _view was set to null in the action, then we dont need a view
        $this->_view = ($this->_view==='') ? $action : $this->_view; //if no view was set, use $action as view name (for actionless views)
        $smarty = wheel::smarty();
        if (!$this->_skipAssign) foreach ($this as $key=>$value) if ($key[0]!='_') $smarty->assign($key, $value);
        $controller = str_replace('Controller','',get_class($this));
        $tpl = 'views/'.str_replace('_', '/', $controller).'/'.$this->_view.'.tpl';
        //gulp the view into a buffer and set it to viewContent for the dispatch/_renderLayout to handle
        ob_start();
        $smarty->display($tpl);
        $viewContent = ob_get_contents();
        ob_end_clean();
        if (!$this->_skipAssign) $smarty->clear_all_assign();
        if (strpos($viewContent, "<b>Warning</b>:  Smarty error: unable to read resource:")!==false)
        throw new wheelException('The view "'.$this->_view.'" was not found at ' . $tpl);
        $this->viewContent = $viewContent;
        $this->_skipAssign = false; //reset this to make sure subsequent actions don't get tainted
    }
    /**
     * render the the overall layout.
     */
    function _renderLayout() {
        if ($this->_layout==null) return; //if _layout was set to null in the action, we dont need a layout
        $smarty = wheel::smarty();
        foreach ($this as $key=>$value) {
            if ($key[0]!='_') $smarty->assign($key, $value); //set smarty vars
        }
        $tpl = 'layouts/'.str_replace('_', '/', $this->_layout).'.tpl';
        ob_start();
        $smarty->display($tpl);
        $this->layoutContent = ob_get_contents();
        ob_end_clean();
        if (strpos($this->layoutContent, "<b>Warning</b>:  Smarty error: unable to read resource:")!==false)
        throw new wheelException('The layout "'.$this->_layout.'" was not found at ' . $tpl);
        $smarty->clear_all_assign();
        return $this->layoutContent;
    }
    /**
     * load and execute another controller->action; keep url the same.
     * @param string $controller the controller to pass to
     * @param string $action the action to be executed
     * @return array an array telling ::dispatch what to do
     */
    function _forward($controller, $action) {
        return array('directive'=>'forward', 'controller'=>$controller, 'action'=>$action);
    }
    /**
     * physically forward to another controller->action
     * @param string $controller the controller to pass to
     * @param string $action the action to be executed
     * @param array $param the associative array of parameters to pass via GET
     * @param string $pattern the format string which determines how $param is translated in the URL
     * @return array an array telling ::dispatch what to do
     */
    function _redirect($controller, $action, $param, $pattern=null) {
        //TODO: take the controller, action, param and pattern and generate a URL, then redirect to it.
    }
    /**
     * physically forward to  URL
     * @param string $url the url to which we want to forward.
     * TODO: check for validity of URL.
     */
    function _redirectURL($url) {
        header('Location: '.$url);
    }
}
/**
 * the abstract singleton that does all the footwork
 * @package wheelvc
 */
abstract class wheel {
    public static $vars = array();
    public static $conf = array();
    public static $smarty; //smarty singleton.
    /**
    * dispatch to the right controller/action
    * @param string $controller the controller to be invoked; object or string; passing an object makes all operations use that same object. passing a string creates a new object of the controller
    * @param string $action the action to be executed
    * @param array $param parameters passed from outside (by default, null and taken from _REQUEST and _POST in _execute
    * @param bool $partial whether or not this is a partial view
    */
    public static function dispatch($controller, $action, $viewOnly = false) {
        if (is_string($controller))
        $control = self::inst($controller);
        else
        $control = $controller;
        $action = ($action!='') ? $action : '';
        $result = $control->_execute($action);
        if (is_array($result)) {
            switch ($result['directive']) {
                case 'forward': {
                    return wheel::dispatch($result['controller'], $result['action'], $viewOnly);
                    break;
                }
                default: {
                    break;
                }
            }
        }
        //TODO: do i need to do something differently here if _view==null?
        $control->_render($action);
        if ($viewOnly || $control->_layout==null)
        return $control->viewContent;
        else
        return $control->_renderLayout();
    }
    /**
     * return an instance of the controller; load the file for the controller from controllers dir if needed
     * @param string $controller the controller to be loaded
     */
    public static function inst($controller) {
        $controllerName = $controller.'Controller';
        $fname = str_replace('_', '/', $controller).'.php';
        if (!class_exists($controllerName)) {
            if (!include(wheel::$conf['local_inc_dir'].'/controllers/'.$fname))
            throw new wheelException('Could not load file for controller '. $controller.' - please verify file is in the right location');
        }
        return new $controllerName();
    }
    /**
     * function to load up the smarty templating engine on demand
     * TODO: figure out what smarty cache does and if we need it
     */
    public static function smarty() {
        if (!wheel::$smarty) {
            require_once(wheel::$conf['smarty_lib_dir'].'/Smarty.class.php');
            wheel::$smarty = new Smarty();
            wheel::$smarty->template_dir = wheel::$conf['local_inc_dir'].'/tpl';
            wheel::$smarty->compile_dir = (wheel::$conf['env']=='dev') ? '/tmp' : wheel::$conf['smarty_dir'].'/templates_c';
            wheel::$smarty->cache_dir = wheel::$conf['smarty_dir'].'/cache';
            wheel::$smarty->config_dir = wheel::$conf['smarty_dir'].'/configs';
        }
        return wheel::$smarty;
    }
}
/**
 * our own exception class, just in case.
 * @package wheel
 */
class wheelException extends Exception {}
/**
 * handle an exception - send to internal fail->puke
 * @param Exception $e the exception to be handled
 */
function wheelExceptionHdl ($e) {
    $fail = wheel::inst('internal_fail');
    $fail->message = $e->getMessage();
    $fail->code = $e->getCode();
    $fail->trace = $e->getTraceAsString();
    $content = wheel::dispatch($fail, 'puke');
    echo $content;
}
function dump($var, $return=false) {
    $dump = print_r($var,true);
    $dump = preg_replace("/\[(.+)\]/","[ <strong>$1</strong> ]",$dump);
    $dump = preg_replace("/([ a-z]+)[\n]([ ]*)\(/i","<strong>$1 </strong>\n$2(",$dump);
    $dump = "<pre>$dump</pre>";
    if ($return) return $dump;
    echo $dump;
}
