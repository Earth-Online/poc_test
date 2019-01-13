<?php
require 'zzz_class.php';
if ($conf['isinstall']==0) error('很抱歉!程序未安装, <span id=time></span>即将进入安装界面',SITE_PATH.'install/');
if($conf['wapmark']=='1' && !defined('ISWAP') && is_mobile()){
  phpgowap($_SERVER['REQUEST_URI']);
}elseif($conf['wapmark']=='0' && defined('ISWAP')){ 
  phpgo(SITE_PATH);exit;
}
if (!defined('TPL_DIR')){
	$data = db_load_one('language',"l_alias='".LANGUAGE."'",'pctemplate,waptemplate,pchtmlpath,waphtmlpath,sitekeys,sitedesc');
	define('PCTPL',$data['pctemplate']);
	define('WAPTPL',$data['waptemplate']);
	defined('ISWAP') ? define('TPL_DIR',SITE_DIR . 'template/wap/'.WAPTPL.$data['waphtmlpath']): define('TPL_DIR', SITE_DIR . 'template/pc/'.PCTPL.$data['pchtmlpath']);
	defined('ISWAP') ? define('TPL_PATH', SITE_PATH . 'template/wap/'.WAPTPL) : define('TPL_PATH', SITE_PATH . 'template/pc/'.PCTPL);
	defined('ISWAP') ? define('HTML_PATH', SITE_PATH . 'wap/') : define('HTML_PATH', SITE_PATH );
	defined('ISWAP') ? define('HTML_DIR', SITE_DIR . 'wap/') : define('HTML_DIR', SITE_DIR );
	$GLOBALS['sitekeys']=$data['sitekeys'];
	$GLOBALS['sitedesc']=$data['sitedesc'];	
	unset($data);
}
 require 'zzz_template.php';
 if (conf('webmode')==0) error(conf('closeinfo'));
 $location=getlocation();
 ParseGlobal(G('sid'),G('cid'));
 //echop($location);die;
 switch ($location) {
	case 'about':
	 	$tplfile= TPL_DIR . G('stpl');
		break; 
	case 'brand':		
		$stpl=splits(db_select('brand','b_template',"bid=".G('bid') or "b_name='".G('bname')."'"),',');
		if (defined('ISWAP')){
		  $tplfile=isset($stpl[1]) ? $stpl[1] : $stpl[0];
		}else{
		  $tplfile=$stpl[0];	
		}
	 	$tplfile=empty($tplfile) ? TPL_DIR .'brand.html' : TPL_DIR . $tplfile ;
		break;	
	case 'brandlist':
		$tplfile=isset($stpl) ? TPL_DIR .  $stpl: TPL_DIR . 'brandlist.html'; 
		$GLOBALS['tid']='-1';
		break;
	case 'content':		
	 	$tplfile= TPL_DIR . G('ctpl');
		break;	
	case 'list':
	 	$tplfile= TPL_DIR . G('stpl');
		break;
	case 'taglist':
		$tplfile=TPL_DIR . 'taglist.html'; 
		$GLOBALS['tid']='-1';
		break;
	case 'user':
	 	$tplfile= TPL_DIR . 'user.html'; 
		break;
	case 'search':
	 	$tplfile= TPL_DIR . 'search.html'; 
		break;
	case 'links':
	 	header ('location:'.G('pageurl'));
		break;
	case '':
	 	//error ('404,访问地址不存在或已经失效！');
		break;	
	default:
	 	$tplfile=TPL_DIR . $location.'.html'; 
		break; 		
 }
 if ($location=='user'){	
	$user_tpl='';$logintype='';$backurl='';
	$act=getform('act','get');
	$GLOBALS['logintype']=$logintype=getform('type','get');
	$GLOBALS['backurl']=$backurl=getform('backurl','get');
	if (empty($act)){
		$path=geturl_path();
		if(SITE_PATH=='/'){
			$act=defined('ISWAP') ? $path[2] : $path[1];
		}else{
			$act=defined('ISWAP') ? $path[3] : $path[2];	
		}
	}
	$uid=get_session('uid');
	$gid=get_session('gid');
	$GLOBALS['sid']='-1';
	$GLOBALS['tid']='-1';
	$GLOBALS['pid']='-1';
	if (empty($uid)){
		$act=empty($act) ? 'login': $act;
		$incfile=TPL_DIR. 'user'.$act.'.html';		
		switch ($act) {				
		  case "reg":case "forget":case "login":case "login":
			  $tplfile=TPL_DIR. 'user'.$act.'.html';
			  $user_tpl = is_file($tplfile) ? load_file($tplfile) :  load_file(PLUG_DIR.'template/'.$act.'.tpl');											
		  break;
		default: 
		  phpgo(SITE_PATH.'?location=user&act=login&type='.$logintype.'&backurl='.$backurl)	;		
		}			
	}else{
		$act=isnul($act) ? 'userinfo': $act;		
		$incfile=TPL_DIR. $act.'.html';
		switch ($act) {		
		  case "reg":case "forget":case "login":		
			  phpgo(SITE_PATH.'?user/');
			  break;
		  case "userinfo":
			  $user_tpl = load_file($incfile);				
			  break; 
		  case "loginout":	
			  phpgo(SITE_PATH.'form/?loginout');
			  break; 
		  default:
			  $user_tpl = load_file($incfile);
		  }
	}
	$GLOBALS['act']=$act;
	$zcontent = $user_tpl;
	if($logintype=='open'){
		$zcontent=str_replace('{zzz:top}','',$zcontent);
		$zcontent=str_replace('{zzz:foot}','',$zcontent);
	}
	$parser = new ParserTemplate();
	$zcontent = $parser->parserCommom($zcontent); // 解析模板
	$zcontent=str_replace('[login:type]',$logintype,$zcontent);
	$zcontent=str_replace('[login:backurl]',$backurl,$zcontent);
	echo $zcontent;	
 }elseif($conf['iscache']==1){
	$cachefile = RUN_DIR . 'cache/html/'.md5($location.G('sid').G('id')). '.tpl' ; 
 	if (!check_file($cachefile) || time_file($tplfile) > time_file($cachefile) ||time_file($cachefile)> time(date('Y-m-d H:i:s',strtotime('-'.$conf['cachetime'].' hour'))) ){	  
		$zcontent = load_file($tplfile,$location);		
		$parser = new ParserTemplate();
		$zcontent = $parser->parserCommom($zcontent); // 解析模板
		create_file($cachefile, $zcontent);
	}else{
		echo load_file($cachefile);	
	}
 }elseif($conf['runmode']==0|| $conf['runmode']==2 || $location=='form' ||$location=='screen' || $location=='app'){
	$zcontent = load_file($tplfile,$location);	
	$parser = new ParserTemplate();
	$zcontent = $parser->parserCommom($zcontent); // 解析模板
	echo $zcontent;	
 }elseif($conf['runmode']==1 || $location=='sitemap' || $location=='sitexml'){
	$url=$_SERVER['REQUEST_URI'];
	$url=str_replace($conf['siteext'],'',$url);
	$url=str_replace('?','',$url);	
	$urlarr=splits($url,'/');
	if(count($urlarr)>3){
		$url='';		
		foreach( $urlarr as $key=>$value){
			$url.= $key<3 ?  '/'.$value : '&'. $value;
		}
	}	
	$url=ltrim($url,'/');
	empty($url) and $url='index';
	if($location=='sitemap' || $location=='sitexml') $url=$location;
	$htmlpath = HTML_PATH. $conf['htmldir'].$url.'.html';
	$htmlfile = HTML_DIR . $conf['htmldir'].$url.'.html'; 
	$zcontent = load_file($tplfile,$location);		
	$parser = new ParserTemplate();
	$zcontent = $parser->parserCommom($zcontent); // 解析模板		
	create_file($htmlfile, $zcontent);
	//echop ($htmlfile);die;
	phpgo($htmlpath);
}else{
	error ('404，您访问的页面不存在',SITE_PATH);
}