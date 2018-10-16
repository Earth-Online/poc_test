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
class sinaconnect_controller extends common
{
	function index_action()
	{
		if($_COOKIE["uid"]!=""&&$_COOKIE["username"]!="")
		{
			$this->obj->ACT_msg("index.php","您已经登录了！");
		}
		if($this->config["sy_sinalogin"]!="1")
		{
			$this->obj->ACT_msg("index.php","对不起，新浪登录已关闭！");
		}
		  include_once( APP_PATH.'api/weibo/saetv2.ex.class.php' );
		 define( "WB_AKEY" ,$this->config['sy_sinaappid']);
		 define( "WB_SKEY" , $this->config['sy_sinaappkey']);
		 define( "WB_CALLBACK_URL" , $this->config['sy_weburl'].'/index.php?m=sinaconnect' );
		 $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
		if (isset($_GET['code'])) {
			$keys = array();
			$keys['code'] = $_GET['code'];
			$keys['redirect_uri'] = WB_CALLBACK_URL;
			$token = $o->getAccessToken( 'code', $keys ) ;
			if($token['access_token'])
			{
				$tokens = 	$token['access_token'];
				$tokenuid =  $token['uid'];
				 if($tokenuid>0)
			     {
					$userinfo = $this->obj->DB_select_once("member","`sinaid`='$tokenuid'");
					if(is_array($userinfo))
					{
						if($this->config[sy_uc_type]=="uc_center")
						{
							$this->obj->uc_open();
							$user = uc_get_user($userinfo[username]);
							$ucsynlogin=uc_user_synlogin($user[0]);
  							
							$this->obj->ACT_msg("index.php","登录成功！",9);

						}else{
							$this->unset_cookie();
							$this->add_cookie($userinfo[uid],$userinfo[username],$userinfo[salt],$userinfo[email],$userinfo[password],$userinfo[usertype]); 
							$this->obj->ACT_msg("index.php","登录成功！",9);
						}
					}else{
						$_SESSION['sinaid'] = $tokenuid;
						$_SESSION['sinainfo'] = "您已成功登录新浪微博，请先绑定您的账户！";
						$this->obj->ACT_msg($this->url("index","login",array("usertype"=>"2")),"您已成功登录新浪微博，请先绑定您的账户！",9);

					}
			     }else{  
				 	$this->obj->ACT_msg($this->config['sy_weburl'],"新浪微博授权失败，请重新授权！");

				}
			}
		}else{
			$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );
			echo "<script>window.location.href='".$code_url."';</script>";
		}
	  }
}

