<?php
require '../inc/zzz_class.php';
if (get_session('adminid')>0)  phpgo("index.php");
$act=getform('act','get');
switch ($act) {	
	case 'loginesc':
		login_esc();
		break;
	case 'loginon':
		$adminname=safe_word(getform('adminname','post','nul'));
		$adminpass=getform('adminpass','post');
		$question=safe_word(getform('question','post'));
		$answer=safe_word(getform('answer','post'));
		is_null(get_cookie('adminname'))	?	$code=getform('code','post','code') :	'';
		if (isnul($adminpass)) {
			db_count('user',"username = '". $adminname ."' and question = '". $question ."' and answer='".$answer."'")>0 ?: back('登录失败,问题和答案错误!');
		}else{
			db_count('user',"username = '". $adminname ."' and password='".md5_16($adminpass)."'")>0 ?: back('登录失败,用户名或密码错误!');
		}
        $lowpass=array('123456','admin','admin888','111111','000000','0','000');
        in_array($adminname,$lowpass) and set_cookie('adminpass','1');
        conf('adminpath')=='admin/' and set_cookie('adminpath','1');
		login_in($adminname);
		phpgo(http_url_path());
		break;
	default:
	include parse_admin_tlp('login');
}

function login_in($username){
	$data=db_load_sql("select uid,username,u_onoff,face,gid,g_onoff,g_name,g_menu,g_sort,g_mark,isadmin,logincount from [dbpre]user as a, [dbpre]user_group as b where username='".$username."' and u_gid=gid");	
	foreach ($data as $value) {	
		$value['isadmin']==1 ?: 	back($username."对不起，你不是管理员");
		$value['g_onoff']==1 ?: 	back("对不起，您所在用户组已被禁用");
		$value['u_onoff']==1 ?: 	back("对不起，您的账号已被禁用");
		$GLOBALS['adminname']		=set_cookie("adminname",$value['username']);	
		$GLOBALS['admingroup']		=set_session("admingroup",$value['g_name']);	
		$GLOBALS['adminid']			=set_session("adminid",$value['uid']);
		$GLOBALS['admingid']		=set_session("admingid",$value['gid']);
		$GLOBALS['adminmenu']		=set_session("adminmenu",$value['g_menu']);	
		$GLOBALS['adminsort']		=set_session("adminsort",$value['g_sort']);
		$GLOBALS['adminmark']		=set_session("adminmark",$value['g_mark']);
		$GLOBALS['adminrand']		=$adminrand=set_session("gname",rand(100000,999999));
		if (empty($value['face'])){
			set_cookie("adminface","../plugins/face/face1.png");
		}elseif(lenstr($value['face'])<11){
			set_cookie("adminface","../plugins/face/". $value['face']);
		}else{
			set_cookie("adminface", $value['face']);
		}
		$GLOBALS['gname']			=set_session("gname",$value['g_name']);
		arr_add($val,'lastlogintime', date('Y/m/d H:i:s'));
		arr_add($val,'lastloginip',ip());
		arr_add($val,'adminrand',$adminrand);
		arr_add($val,'logincount',$value['logincount']+1);
		db_update('user',$value['uid'],$val);	
	}
}