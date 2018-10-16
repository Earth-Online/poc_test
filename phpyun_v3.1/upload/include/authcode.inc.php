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
error_reporting(0);
session_start();
function random_language($len) {
	$srcstr = "qwertyuiopasdfghjklzxcvbnm";
	mt_srand();
	$strs = "";
	for ($i = 0; $i < $len; $i++) {
		$strs .= $srcstr[mt_rand(0, 10)];
	}
	return $strs;
}
function random_shuzi($len) {
	$srcstr = "0123456789";
	mt_srand();
	$strs = "";
	for ($i = 0; $i < $len; $i++) {
		$strs .= $srcstr[mt_rand(0, 9)]; 
	}
	return $strs;
}
function random($len) {
	$srcstr = "1a2s3d4f5g6hj8k9l0qwertyuiopzxcvbnm";
	mt_srand();
	$strs = "";
	for ($i = 0; $i < $len; $i++) {
		$strs .= $srcstr[mt_rand(0, 10)];
	}
	return $strs;
}
function authcode($str=4,$width="50",$height=25,$codetype="png",$code_type="3"){
	if($code_type==3){
		$str = random($str); 
	}else if($code_type==2){
		$str = random_language($str); 
	}else{
		$str = random_shuzi($str);
	}
	$width = $width; 
	$height = $height; 
	if($codetype=="png"){
		@header("Content-Type:image/png");
	}elseif($codetype=="jpg"){
		@header("Content-Type:image/jpeg");
	}else{
		@header("Content-Type:image/gif");
	}
	$im = imagecreate($width, $height);

	$back = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);

	$pix = imagecolorallocate($im, 187, 230, 247);

	$font = imagecolorallocate($im, 41, 163, 238);

	mt_srand();
	for ($i = 0; $i < 1000; $i++) {
		imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pix);
	}
	imagestring($im, 5, 7, 5, $str, $font);
	imagerectangle($im, 0, 0, $width -1, $height -1, $font);
	imagepng($im);
	imagedestroy($im);
	$str = md5($str);
	$_SESSION["authcode"] = $str;
}
include(dirname(dirname(__FILE__))."/plus/config.php");
authcode($config['code_strlength'],$config['code_width'],$config['code_height'],$config['code_filetype'],$config['code_type']);
?>