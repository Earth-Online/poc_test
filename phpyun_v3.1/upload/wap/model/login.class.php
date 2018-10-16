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
class login_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		if($this->uid || $this->username)
		{
			$this->wapheader('member/index.php');
		}
		if($_POST['submit'])
		{
			if($_POST['wxid'])
			{
				$wxparse = '&wxid='.$_POST['wxid'];
			}
			$usertype=$_POST['usertype']?intval($_POST['usertype']):1;
			$username = str_replace('\\','',$_POST['username']);

			if(!$this->CheckRegUser($username))
			{
				$this->wapheader('index.php?m=login&','无效的用户名！');
			}
			if($usertype>0 && $username!='')
			{
				$userinfo = $this->obj->DB_select_once("member","`username`='".str_replace('\\','',$_POST['username'])."' and usertype='".$usertype."'","username,usertype,password,uid,salt");
				if(!is_array($userinfo))
				{
					$this->wapheader('index.php?m=login&usertype='.$usertype.$wxparse.'&','用户不存在');
				}
				$pass = md5(md5($_POST['password']).$userinfo['salt']);
				if($pass!=$userinfo['password'])
				{
					$this->wapheader('index.php?m=login&usertype='.$usertype.$wxparse.'&','密码不正确！');
				
				}else{
					
					if($_POST['wxid'])
					{
						$this->obj->update_once('member',array('wxid'=>''),array('wxid'=>$_POST['wxid']));
						$this->obj->update_once('member',array('wxid'=>$_POST['wxid']),array('uid'=>$userinfo['uid']));
					}

					setcookie("uid",$userinfo['uid'],time() + 86400, "/");
					setcookie("username",$userinfo['username'],time() + 86400, "/");
					setcookie("usertype",$userinfo['usertype'],time() + 86400, "/");
					setcookie("salt",$userinfo['salt'],time() + 86400, "/");
					setcookie("shell",md5($userinfo['username'].$userinfo['password'].$userinfo['salt']), time() + 86400,"/");
					if($_POST['wxid']){
						
						$this->wapheader('index.php?','绑定成功，请按左上方返回进入微信客户端');
						
					}else{
						
						$this->wapheader('member/index.php');	
					}
				}
			}else{
				$this->wapheader('index.php?m=login&','数据错误！');
			}
		}
		if($_GET['usertype']=="2")
		{
			$this->yunset("title","企业会员登录");
		}else{
			$this->yunset("title","个人会员登录");
		}
		$this->yuntpl(array('wap/login'));
	}
}
?>