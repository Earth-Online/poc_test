<?php
require '../../../inc/zzz_class.php';
$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = getform('action','get');
$upfolder = getform('upfolder','get');
switch ($action) {
    case 'config':
        $result = json_encode($CONFIG);
        break;
    
    /* 上传图片 */
    case 'uploadimage':
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    	$result =tojson(upload($_FILES['upfile'],'image',$upfolder));
        break;   
    /* 上传文件 */
    case 'uploadfile':
       $result =tojson(upload($_FILES['upfile'],'file',$upfolder));
        break;    
    /* 列出图片 */
		 case 'uploadvideo':
			$result =tojson(upload($_FILES['upfile'],'video',$upfolder));
        break;   
    case 'listimage':
		$size=getform('size','get');
		$start=getform('start','get');
		$end = $start + $size;
		$allowFiles=str_replace(",","|",conf('imageext'));
		$path = UPLOAD_DIR.$upfolder.'/';
		$files = getfiles($path, $allowFiles);
		if (! count($files)) {
			return json_encode(array(
				"state" => "no match file",
				"list" => array(),
				"start" => $start,
				"total" => count($files)
			));
		}
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i --) {
			$list[] = $files[$i];
		}
		$result = json_encode(array(
			"state" => "SUCCESS",
			"list" => $list,
			"start" => $start,
			"total" => count($files)
		));		
        break;
    /* 列出文件 */
    case 'listfile':
        $result = getfiles();
        break;
    
    /* 抓取远程文件 */
    case 'catchimage':
		$source=$_POST['source'];
		$list = array();
     	foreach ($source as $imgUrl) {
			$info =down_url($imgUrl,SITE_DIR.conf('uploadpath').$upfolder.'/'); 
			 array_push($list, array(			
				"state" => "SUCCESS",				
				"title" => $info["title"],
				"url" => $info["url"],
				"source"=>$imgUrl
			));
		}
		$result =  json_encode(array(
			'state' => count($list) ? 'SUCCESS' : 'ERROR',
			'list' => $list
		));
        break;
    default:
        $result = json_encode(array(
            'state' => '请求地址出错'
        ));
        break;
}
/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state' => 'callback参数不合法'
        ));
    }
} else {
    echo($result);
}