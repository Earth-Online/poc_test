<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */

if($_GET['id']){
	
	$id = intval($_GET['id']);
	include_once("../outside/".$id.".php");
	$list = $content;
	echo "document.write('$list');";
}
?>