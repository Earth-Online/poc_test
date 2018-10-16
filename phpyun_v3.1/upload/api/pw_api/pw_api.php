<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
error_reporting(0);
define('P_W','admincp');
define('S_DIR',dirname(__FILE__)."/");
define('R_P',S_DIR.'/');
define('D_P',R_P);
define('PHPYUN',dirname(dirname(dirname(__FILE__))));
require_once(S_DIR.'pw_config.php');
require_once(S_DIR.'security.php');
require_once(S_DIR.'pw_common.php');
require_once(S_DIR.'class_base.php');
//require_once(PHPYUN.'/global.php');
//require(MODEL_PATH.'class/action.class.php');
//$obj= new action($db,$phpyun,$db_config["def"]);
$api = new api_client();
$response = $api->run($_POST + $_GET);
if($response) {
	echo $api->dataFormat($response);
}

?>