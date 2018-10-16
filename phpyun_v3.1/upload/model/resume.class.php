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
class resume_controller extends common
{
    function index_action()
    {
		if($_GET['uid'])
		{
			$def_job=$this->obj->DB_select_once("resume","`r_status`!='2' and `uid`='".(int)$_GET['uid']."'","def_job");
			if(!is_array($def_job))
			{
				$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"没有找到该人才！");
			}else{
				if($def_job['def_job']=="0")
				{
					$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"还没有创建简历！");
				}else{
					$_GET['id']=$def_job['def_job'];
				}
			}
			$this->resume_select($def_job['def_job']);

		}

		if($_GET['id'])
		{
			$this->obj->DB_update_all("resume_expect","`hits`=`hits`+1","`id`='".(int)$_GET['id']."'");
		}
		if($_COOKIE['usertype']=="2")
		{
			$this->yunset("uid",$_COOKIE['uid']);
			$this->obj->DB_update_all("userid_job","`is_browse`=2","`com_id`='".$this->uid."' and `eid`='".(int)$_GET['id']."'");
			$this->unset_remind("userid_job",'2');
		}

		if($_GET['type']=="word")
		{
			$resume=$this->obj->DB_select_once("down_resume","`eid`='".(int)$_GET['id']."' and `downtime`='".$_GET['downtime']."'");
			if(is_array($resume) && !empty($resume))
			{
				$this->yun_tpl(array('wordresume'));
				die;
			}
		}
		$this->seo("resume");

		$this->yun_tpl(array('index'));


    }
    function sendresume_action(){
		$this->yunset("type",$_GET['type']);
		$this->yunset("job_link",$_GET['job_link']);
		$this->yun_tpl(array('sendresume'));
    }
    function resume_share_action()
    {
		if($_POST)
		{
			if($_POST['femail']=="" || $_POST['myemail']=="" || $_POST['authcode']=="")
			{
				echo "请完整填写信息！";die;
			}
			if(md5($_POST['authcode'])!=$_SESSION['authcode'])
			{
				echo "验证码不正确！";die;
			}
			if($_COOKIE["sendresume"]==$_POST['id'])
			{
				echo "请不要频繁发送邮件！同一简历发送间隔为两分钟！";die;
			}
			$contents=file_get_contents($this->config[sy_weburl]."/index.php?m=resume&c=sendresume&id=".$_POST['id']);
			$smtp = $this->email_set();
			$smtpusermail =$this->config['sy_smtpemail'];
			$myemail = iconv("UTF-8","GB2312//IGNORE",$_POST['myemail']);

			$sendid = $smtp->sendmail($_POST['femail'],$smtpusermail,"您的好友".$myemail."向您推荐了简历！",$contents,"HTML","","","",$myemail);
			if($sendid)
			{
				echo 1;
			}else{
				echo "邮件发送错误 原因：1邮箱不可用 2网站关闭邮件服务";die;
			}
			SetCookie("sendresume",$_POST['id'],time() + 120, "/");
			die;
		}
		$this->seo("resume_share");
		$this->yunset("id",$_GET['id']);
		$this->yun_tpl(array('resume_share'));
    }
    function user_resume($uid="",$eid="")
    {
    	$status=0;
		if($this->uid==$uid && $this->username && $_COOKIE['usertype']==1)
		{
			$status=1;
			$this->yunset("user_status",$status);
		}
		if($this->uid && $this->username && $_COOKIE['usertype']==2){
			$row=$this->obj->DB_select_once("down_resume","`eid`='".$eid."' and comid='".$this->uid."'");
			if(is_array($row))
			{
				$status=1;
				$this->yunset("com_status",$status);
			}
		}
    }
    function send_email_resume_action()
    {
    	$this->yun_tpl(array('send_email_resume'));
    }
}
?>