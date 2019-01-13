<?php
require '../../inc/zzz_class.php';
if ((get_session('uid')>0)){
	echo('document.write("  <a href='.SITE_PATH.'?user/><i class=\'ico login\'></i> 会员中心</a><a href='.SITE_PATH.'?user/loginout><i class=\'ico reg\'></i> 登出</a>")');
}else{
	echo('document.write("  <a href='.SITE_PATH.'?user/login><i class=\'ico login\'></i> 登录</a><a href='.SITE_PATH.'?user/reg><i class=\'ico reg\'></i> 注册</a>")');
}
?>