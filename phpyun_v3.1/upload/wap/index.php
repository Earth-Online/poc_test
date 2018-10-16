<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
include(dirname(dirname(__FILE__))."/global.php");
$model = $_GET['m'];
$action = $_GET['c'];
$usertype=$_COOKIE["usertype"];

if($_GET['m'] && !ereg("^[0-9a-zA-Z\_]*$",$_GET['m'])){
	
	$_GET['m'] = 'index';
}
if($model=="")	$model="index";
if($action=="")	$action = "index";

if(!is_file(MODEL_PATH.$model.'.class.php'))
{
	$controller='index';
	$action='index';
}

require(MODEL_PATH.'class/common.php');
require("model/".$model.'.class.php');
$conclass=$model.'_controller';
$actfunc=$action.'_action';
$views=new $conclass($phpyun,$db,$db_config[def],"index");
if(!method_exists($views,$actfunc)){
	$views->DoException();
}

$views->$actfunc();
?>