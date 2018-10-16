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
		if($_COOKIE["uid"]!=""&&$_COOKIE["username"]!="")
		{
			if($_GET["type"]=="out")
			{
				if($this->config["sy_uc_type"]=="uc_center")
				{
					$this->obj->uc_open();
					$logout = uc_user_synlogout();
				}elseif($this->config["sy_pw_type"]){
					include(APP_PATH."/api/pw_api/pw_client_class_phpapp.php");
					$username=$_SESSION["username"];
					$pw=new PwClientAPI($username,"","");
					$logout=$pw->logout();
					$this->unset_cookie();
				}else{
					$this->unset_cookie();
				}
			}else{
				$this->obj->ACT_msg("index.php", $msg = "您已经登录了！");
			}
		}

		if($_GET['backurl']=='1')
		{
			setCookie("backurl",$_SERVER['HTTP_REFERER'],time()+60);
		}
		if(!$_GET['usertype'])
		{
			$_GET['usertype']=1;
		}
		$this->yunset("usertype",$_GET['usertype']);
		$this->yunset("loginname",$_COOKIE['loginname']);
		$this->seo("login");
		$this->yun_tpl(array('index'));
	}

	function loginsave_action()
	{
		$username=iconv("utf-8","gbk",$_POST['username']);

		if($_COOKIE['uid']!=""&&$_COOKIE['username']!="")
		{
			$this->ajaxlogin($_POST['comid'],"您已经登陆了，您不是个人用户！");
			echo "您已经登录了！";die;
		}
		
		if($_POST['path']!="index")
		{
			if(strstr($this->config["code_web"],'前台登陆'))
			{
				if(md5($_POST["authcode"])!=$_SESSION["authcode"])
				{
					$this->ajaxlogin($_POST['comid'],"验证码错误!");
					echo "验证码错误!";die;
				}
			}
		}
		if(!$this->CheckRegUser($username))
		{
			echo "无效的用户名!";die;
		}
		if($username!="")
		{
			if($this->config['sy_uc_type']=="uc_center")
			{
				$this->obj->uc_open();
				list($uid, $username, $password, $email) = uc_user_login($username, $_POST['password']);
				
				if($uid<1)
				{
					$user = $this->obj->DB_select_once("member","`username`='".$uname."'","username,email,uid,password,salt");
					$pass = md5(md5($_POST['password']).$user['salt']);
					if($pass==$user['password'])
					{
						$uid = $user['uid'];
						uc_user_register($user['username'],$_POST['password'],$user['email']);
						list($uid, $username, $password, $email) = uc_user_login($uname, $_POST['password']);
					}
				}
				
				if($uid > 0) {
					$ucsynlogin=uc_user_synlogin($uid);
					$msg =  '登录成功！';
					$user = $this->obj->DB_select_once("member","`username`='".$username."' and `usertype`='2'","uid");
					if($_SESSION['qq']['openid'])
					{
						$this->obj->DB_update_all("member","`qqid`='".$_SESSION['qq']['openid']."'","`username`='".$username."'");
						unset($_SESSION['qq']);
					}

					if($_SESSION['sinaid'])
					{
						$this->obj->DB_update_all("member","`sinaid`='".$_SESSION['sinaid']."'","`username`='".$username."'");
						unset($_SESSION['sinaid']);unset($_SESSION['sinainfo']);
					}
					if($_POST['usertype']=="2")
					{
						if(!is_array($user))
						{
							echo "企业没有该用户！";die;
						}

					}
					if($this->config['user_status']=="1" && $_POST['usertype']=="1")
					{
						if($_POST['usertype']=="1")
						{
							if(is_array($user))
							{
								$this->unset_cookie();
								echo "个人不存在该用户！";die;
							}else{
								
								$userin=$this->obj->DB_select_once("member","`username`='".$username."'","`email_status`");
								if($userin['email_status']!="1")
								{
									echo "您的账户还未激活，请先激活！";die;
								}
							}
						}
					}
					if($_POST['loginname']){
						setcookie("loginname",$username,time()+8640000);
					}
					$this->autoupjob($user['uid'],$_POST['usertype']);
					echo $ucsynlogin;
					echo 1;die;
				} elseif($uid == -1) {
					$msg =  '用户不存在,或者被删除';
				} elseif($uid == -2) {
					$msg =  '密码错误';
				} else {
					$msg = '该用户未定义!';
				}
				$this->ajaxlogin($_POST['comid'],$msg);
				echo $msg;die;
			}else{
				$user = $this->obj->DB_select_once("member","`username`='".$username."'","`pw_repeat`,`pwuid`,`uid`,`username`,`salt`,`email`,`password`,`usertype`,`status`,`email_status`");
				if($this->config['sy_pw_type']=="pw_center")
				{
					if($user['pw_repeat']!="1")
					{
						include(APP_PATH."/api/pw_api/pw_client_class_phpapp.php");
						$pw=new PwClientAPI($username,$_POST['password'],"");
						$pwuser =$pw->user_login();
						if($pwuser['uid']>0)
						{
							if(empty($user))
							{
								$user = $this->newuser($pwuser['username'],$pwuser['password'],$pwuser['email'],$_POST['usertype'],$pwuser['uid'],$qqid);
							}
							if($pwuser['uid']==$user['pwuid'])
							{
								$pwrows=$pw->login($pwuser['uid']);
								$this->add_cookie($user['uid'],$user['username'],$user['salt'],$user['email'],$user['password'],$_POST['usertype']);
								if($_POST['loginname'])
								{
									setcookie("loginname",$username,time()+8640000);
								}
								$this->ajaxlogin($_POST['comid'],"1");
								echo 1;die;
							}else{
								$this->obj->DB_update_all("member","`pw_repeat`='1'","`uid`='".$user['uid']."'");
							}
						}
					}
				}
				if(is_array($user))
				{
					if($user['usertype']==$_POST['usertype'])
					{
						$pass = md5(md5($_POST['password']).$user['salt']);
						if($user['password']==$pass)
						{
							if($user['status']=="2")
							{
								$this->ajaxlogin($_POST['comid'],"您的账号已被锁定!");
								echo "您的账号已被锁定!";die;
							}
							if($_POST['usertype']=="2" && $this->config['com_status']!="1" )
							{
								if($user['status']!="1")
								{
									$this->ajaxlogin($_POST['comid'],"您还没有通过审核!");
									echo "您还没有通过审核!";die;
								}
							}

							if($this->config['user_status']=="1" && $_POST['usertype']=="1")
							{
								if($user['email_status']!="1")
								{
									$this->ajaxlogin($_POST['comid'],"您的账户还未激活，请先激活！");
									echo "您的账户还未激活，请先激活!";die;
								}
							}
							if($_SESSION['qq']['openid'])
							{
								$this->obj->DB_update_all("member","`qqid`='".$_SESSION['qq']['openid']."'","`username`='".$user['username']."'");
								unset($_SESSION['qq']);
							}
							if($_SESSION['sinaid'])
							{
								$this->obj->DB_update_all("member","`sinaid`='".$_SESSION['sinaid']."'","`username`='".$username."'");
								unset($_SESSION['sinaid']);unset($_SESSION['sinainfo']);
							}
							$time = time();
							$ip = $this->obj->fun_ip_get();
							$this->obj->DB_update_all("member","`login_ip`='$ip',`login_date`='$time',`login_hits`=`login_hits`+1","`uid`='".$user['uid']."'");
							$this->unset_cookie();
							$this->add_cookie($user['uid'],$user['username'],$user['salt'],$user['email'],$user['password'],$_POST['usertype']);
							if($qqid)
							{
								$this->obj->DB_update_all("member","`qqid`='$qqid'","`username`='$username'");
							}
							if($_POST['loginname']){
								setcookie("loginname",$username,time()+8640000);
							}
							$this->ajaxlogin($_POST['comid'],"1");
							if($_POST['usertype']=='1'){
								$resume=$this->obj->DB_select_once("resume","`uid`='".$user['uid']."'","`name`,`birthday`");
								if($resume['name']&&$resume['birthday']){
									echo 1;die;
								}else{echo 2;die;}
							}else{$this->autoupjob($user['uid'],$_POST['usertype']);echo 1;die;}
							
							
						}else{
							$this->ajaxlogin($_POST['comid'],"密码不正确！");
							echo "密码不正确！";die;
						}
					}else{
						if($_POST['usertype']=="1")
						{
							$msg = "对不起，没有该个人用户！";
							$this->ajaxlogin($_POST['comid'],$msg);
						}else{
							$msg = "对不起，没有该企业用户！";
						}
						echo $msg;die;
					}
				}else{
					$this->ajaxlogin($_POST['comid'],"该用户不存在！");
					echo "该用户不存在！";die;
				}
			}
		}else{
			echo "用户名不能为空！";die;
		}
	}
	function newuser($username,$password,$email,$usertype,$winduid,$qqid='')
	{
		$salt = substr(uniqid(rand()), -6);
		$pass = md5($password.$salt);
		$ip = $this->obj->fun_ip_get();
		$time = time();
		$satus = $this->config["user_status"];
		$this->obj->DB_insert_once("member","`username`='$username',`password`='$pass',`email`='$email',`usertype`='$usertype',`status`='$satus',`salt`='$salt',`reg_date`='$time',`reg_ip`='$ip',`pwuid`='$winduid'");
		$this->unset_cookie();
		$new_info = $this->obj->DB_select_once("member","`username`='$username'");
		$userid = $new_info[uid];
		if($this->config["sy_pw_type"]=="pw_center")
		{
			$this->obj->DB_update_all("member","`pwuid`='$pwuid'","`uid`='$userid'");
		}
		$this->add_cookie($userid,$username,$salt,$email,$pass,$usertype);
		if($usertype=="1")
		{
			$table = "member_statis";
			$table2 = "resume";
			$value="`uid`='$userid'";
			$value2 = "`uid`='$userid',`email`='$email',`telphone`='$moblie'";
		}elseif($usertype=="2"){
			$table = "company_statis";
			$table2 = "company";
			$value="`uid`='$userid',".$this->rating_info();
			$value2 = "`uid`='$userid',`linkmail`='$email',`name`='$unit_name',`address`='$address'";
		}
		if($qqid)
		{
			$this->obj->DB_update_all("member","`qqid`='$qqid'","`uid`='$userid'");
		}
		$this->obj->DB_insert_once($table,$value);
		$this->obj->DB_insert_once($table2,$value2);
		return $new_info;
	}
	function ajaxlogin($comid,$msg)
	{
		if((int)$comid>0)
		{
			echo $msg;die;
		}
	}
	function rest_action()
	{
		$this->unset_cookie();
		$url = $this->url("index","login",array("usertype"=>"1"),"1");
		header("Location: ".$url);
	}

	function autoupjob($uid,$usertype){
		
		if($usertype=='2')
		{
			$this->obj->DB_update_all('company_job',"`lastupdate`='".time()."'","`uid`='".$uid."' AND `autotype`='2' AND `autotime`>'".time()."'");
		}

	}
}
