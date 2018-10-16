<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
include_once("../global.php");
include_once("../plus/ad_cache.php");
if($_GET['id']&&$_GET['classid']){
	$ad_id = $_GET['id'];
	$ad_class_id = intval($_GET['classid']);
	if($ad_label[$ad_class_id][$ad_id]['did']==$_SESSION["did"] || $ad_label[$ad_class_id][$ad_id]['did']=="0"){
		$ad_info = $ad_label[$ad_class_id][$ad_id]['html'];
		$ad_info=str_replace("\n","",$ad_info);
		$ad_info=str_replace("\r","",$ad_info);
		$ad_info=str_replace("'","\'",$ad_info);
		echo "document.write('$ad_info');";
	}
}
?>