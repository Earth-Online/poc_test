<?php
require '../inc/zzz_admin.php';
$getModule=getModule();
$type=getform('type','get');
$folder=getform('folder','get');
$module=!empty($getModule) ? $getModule : 'index';
$GLOBALS['type']=$type;
geturl();
switch ($module) {	
	case 'aboutlist':		
		break;
	case 'content':		
		if(ifstrin(G('ID'),'cid')){
			$data=db_load_one('content',G('ID'));			
			$GLOBALS['stype']=isset($data['c_type']) ? $data['c_type'] :$data['C_Type'];
			$GLOBALS['sid']=isset($data['c_sid']) ? $data['c_sid'] :$data['C_SID'];			
		}else{			
			$GLOBALS['ID']='';
		}			
		break;
	case 'contentlist':		
		$sid=G('ID');
		if ($sid==0){
			//$data=db_load_one('sort',"s_type='".$stype."'",'','sid asc');	
			$data=array('s_type'=>$stype,'s_name'=>'','sid'=>'');
		}else{
			$data=db_load_one('sort',$sid);		
		}		
		break;	
	case 'sort':	
		if(ifstrin(G('ID'),'sid')){	
	   		$data=db_load_one('sort',G('sid'));
		} elseif(ifstrin(G('ID'),'pid')){		
			$data=db_load_sql_one("select * from [dbpre]sort where sid=".G('pid'));
		} else{
			$GLOBALS['ID']='';
		}		
	   break;
	case 'custom':
		if (G('ID')) $data=db_load_one('content_custom',G('ID'));	
		break;
	case 'loginout':
		login_out();
		break;
	case 'loginesc':
		login_esc();
		break;
	case 'siteedit':
		 $data = db_load_one('language','l_onoff=1');
		break;
	case 'admin':
		if (G('ID')) $data = db_load_one('user',G('ID'));
		break;
	case 'usergroup':
	case 'admingroup':
		if (G('ID')) $data = db_load_one('user_group',G('ID'));
		break;
	case 'filelist':
		break;
	default:
		if (G('ID')) $data=db_load_one($module,G('ID'));			
}
	//echop ($data);die;
	$GLOBALS['r']=isset($data) ? arr_key($data) : '';
	//echop (parse_admin_tlp($module));die;
	include parse_admin_tlp($module);