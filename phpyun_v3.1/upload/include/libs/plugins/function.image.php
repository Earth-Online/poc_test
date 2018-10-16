<?php
function smarty_function_image($paramer,&$smarty){
		global $db,$config;
	   $width=$paramer[width];
		$height=$paramer[height];
		$uid=$paramer[uid];
		$alt=$paramer[alt];
		$alt=$alt?"alt='".$alt."'":"";
		$action=$paramer[action];
		$action=$action?$action:"moblie";
		$dir=APP_PATH."upload/tel/".$uid."/";
		$name=$action.".gif";
		if(!is_dir($dir))@mkdir($dir,true);
		@chmod($dir,0777);
		if(!file_exists($dir.$name)){
				switch($action){
					case "":
					case "moblie":
					$table="member";
					break;
					case "linkqq":
					case "linktel":
					case "linkphone":
					$table="company";
					break;
					case "telhome":
					case "telphone":
					case "idcard":
					$table="resume";
					break;
				}

			$Info = $db->select_alls("member",$table,"a.`uid`=b.`uid` and a.`uid`='".$uid."'");

			if(is_array($Info)){
				$p=$Info[0][$action];
				if($p==""){
					return iconv('utf8','gbk',"用户未填写");
				}
				if($action=="idcard"){
					$p=substr($p,0,strlen($p)-6).'******';
				}

				$nwidth=$width?$width:130;

				$nheight=$height?$height:23;
				$im=@imagecreate($nwidth,$nheight) or die("Can't initialize new GD image stream");  
 				$background_color=imagecolorallocate($im,255,255,255);  
				$text_color=imagecolorallocate($im,23,14,91);
 				imagefilledrectangle($im,0,0,$nwidth-1,$nheight-1,$background);  
				imagerectangle($im,0,0,$nwidth-1,$nheight-1,$background_color);  
				$randval=$p; 
				imagestring($im,8,10,2,$randval,$text_color);  
				@imagegif($im,$dir.$name); 
				@imagedestroy($im); 
			}else{
				return iconv('utf8','gbk',"用户未填写");
			}
		}


			return  "<img src='".$config[sy_weburl]."/upload/tel/".$uid."/".$name."' ".$alt."/>";


}
?>