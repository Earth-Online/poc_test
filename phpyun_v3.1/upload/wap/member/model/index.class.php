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
class index_controller extends common
{
	function waptpl($tpname)
	{
		$this->yuntpl(array('wap/member/user/'.$tpname));
	}
	function index_action()
	{
		$looknum=$this->obj->DB_select_num("look_resume","`uid`='".$this->uid."'");
		$this->yunset("looknum",$looknum);
		$yqnum=$this->obj->DB_select_num("userid_msg","`uid`='".$this->uid."'");
		$this->yunset("yqnum",$yqnum);
		$statis=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
		$this->yunset("statis",$statis);
		$this->waptpl('index');
	}
	function sq_action()
	{
		if($_GET['del'])
		{
			$userid_job=$this->obj->DB_select_once("userid_job","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			$id=$this->obj->DB_delete_all("userid_job","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			if($id){
				$msg="删除成功!";
				$this->obj->DB_update_all('company_statis',"`sq_job`=`sq_job`-1","`uid`='".$userid_job['com_id']."'");
				$this->obj->DB_update_all('member_statis',"`sq_jobnum`=`sq_jobnum`-1","`uid`='".$userid_job['uid']."'");
			}else{
				$msg="删除失败!";
			}
			$this->wapheader('index.php?c=sq&',$msg);
		}
		$urlarr=array("c"=>"sq","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("userid_job","`uid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$com_id[]=$v['com_id'];
			}
			$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$com_id).")","cityid,uid");
			include APP_PATH."/plus/city.cache.php";
			foreach($rows as $k=>$v)
			{
				foreach($company as $val)
				{
					if($v['com_id']==$val['uid'])
					{
						$rows[$k]['city']=$city_name[$val['cityid']];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->waptpl('sq');
	}
	function collect_action()
	{
		if($_GET['del'])
		{
			$id=$this->obj->DB_delete_all("fav_job","`id`='".$_GET['del']."' and `uid`='".$this->uid."'");
			if($id)
			{
				$msg="删除成功!";
				$this->obj->DB_update_all("member_statis","`fav_jobnum`=`fav_jobnum`-1","uid='".$this->uid."'");
			}else{
				$msg="删除失败!";
			}
			$this->wapheader('index.php?c=collect&',$msg);
		}
		$urlarr=array("c"=>"collect","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("fav_job","`uid`='".$this->uid."'",$pageurl,"10");
		$this->waptpl('collect');
	}
	function password_action()
	{
		if($_POST['submit'])
		{
			$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'");
			$pw=md5(md5($_POST['oldpassword']).$member['salt']);
			if($pw!=$member['password'])
			{
				$this->wapheader('index.php?c=password&','旧密码不正确，请重新输入！');
			}
			if(strlen($_POST['password1'])<6 || strlen($_POST['password1'])>20)
			{
				$this->wapheader('index.php?c=password&','密码长度应在6-20位！');
			}

			if($_POST['password1']!=$_POST['password2'])
			{
				$this->wapheader('index.php?c=password&','新密码和确认密码不一致！');
			}
			if($this->config['sy_uc_type']=="uc_center" && $member['name_repeat']!="1")
			{
				$this->obj->uc_open();
				$ucresult= uc_user_edit($member['username'], $_POST['oldpassword'], $_POST['password1'], "","1");
				if($ucresult == -1)
				{
					$this->wapheader('index.php?c=password&','旧密码不正确，请重新输入！');
				}
			}else{
				$salt = substr(uniqid(rand()), -6);
				$pass2 = md5(md5($_POST['password1']).$salt);
				$this->obj->DB_update_all("member","`password`='".$pass2."',`salt`='".$salt."'","`uid`='".$this->uid."'");
			}
			SetCookie("uid","",time() -286400, "/");
			SetCookie("username","",time() - 86400, "/");
			SetCookie("salt","",time() -86400, "/");
			SetCookie("shell","",time() -86400, "/");

			$this->wapheader('../index.php?','修改成功，请重新登录！');
		}

		$this->waptpl('password');
	}
	function invite_action()
	{
		if($_GET['del'])
		{
			$id=$this->obj->DB_delete_all("userid_msg","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			if($id)
			{
				$msg="删除成功!";
			}else{
				$msg="删除失败!";
			}
			$this->wapheader('index.php?c=invite&',$msg);
		}
		$urlarr=array("c"=>"invite","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("userid_msg","`uid`='".$this->uid."'",$pageurl,"10");
		$this->waptpl('invite');
	}
	function look_action()
	{
		if($_GET['del'])
		{
			$id=$this->obj->DB_delete_all("look_resume","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			if($id)
			{
				$msg="删除成功!";
			}else{
				$msg="删除失败!";
			}
			$this->wapheader('index.php?c=look&',$msg);
		}
		$urlarr=array("c"=>"look","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("look_resume","`uid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['com_id'];
				$eid[]=$v['resume_id'];
			}
			$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$uid).")","`uid`,`name`");
			$resume=$this->obj->DB_select_all("resume_expect","`id` in (".@implode(",",$eid).")","`id`,`name`");
			foreach($rows as $k=>$v)
			{
				foreach($company as $val)
				{
					if($v['com_id']==$val['uid'])
					{
						$rows[$k]['com_name']=$val['name'];
					}
				}
				foreach($resume as $val)
				{
					if($v['resume_id']==$val['id'])
					{
						$rows[$k]['resume_name']=$val['name'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->waptpl('look');
	}
	function addresume_action()
	{
		if($this->config['user_enforce_identitycert']=="1")
		{
			$row=$this->obj->DB_select_once("resume","`idcard_pic`<>'' and `uid`='".$this->uid."'");
			if($row['idcard_status']!="1")
			{
				$this->wapheader('index.php?&',"请先登录电脑客户端完成身份认证！");
			}
		}
		if(!$_GET['eid'])
		{
			$num=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
			$maxnum=$this->config['user_number']-$num['resume_num'];
			$confignum=$this->config['user_number'];
			if($maxnum<=0 &&$confignum!="")
			{
				$this->wapheader('index.php?',"你的简历数已经超过系统设置的简历数了！");
			}
		}else{
			$row=$this->obj->DB_select_once("resume_expect","`id`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			include(PLUS_PATH."job.cache.php");
			$job_classid=@explode(",",$row['job_classid']);
			foreach($job_classid as $v){
				$jobname[]=$job_name[$v];
			}
			$jobname=@implode(",",$jobname);
			$this->yunset("row",$row);
			$this->yunset("jobname",$jobname);
			$skill=$this->obj->DB_select_all("resume_skill","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$work=$this->obj->DB_select_all("resume_work","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$project=$this->obj->DB_select_all("resume_project","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$edu=$this->obj->DB_select_all("resume_edu","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$training=$this->obj->DB_select_all("resume_training","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$cert=$this->obj->DB_select_all("resume_cert","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$other=$this->obj->DB_select_all("resume_other","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$this->yunset("skill",$skill);
			$this->yunset("work",$work);
			$this->yunset("project",$project);
			$this->yunset("edu",$edu);
			$this->yunset("training",$training);
			$this->yunset("cert",$cert);
			$this->yunset("other",$other);
		}
		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		$this->yunset("resume",$resume);
		$this->user_cache();
		$this->city_cache();
		$this->industry_cache();
		$this->job_cache();
		$this->waptpl('addresume');
	}
	function addresumeson_action()
	{
		$tables=array("skill","work","project","edu","training","cert","other");
		

		if($_GET['id'] && in_array($_GET['type'],$tables))
		{
			$row=$this->obj->DB_select_once("resume_".$_GET['type'],"`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			$this->yunset("row",$row);
		}
		$this->user_cache();
		$this->waptpl('addresumeson');
	}
	function info_action()
	{
		if($_POST['submit'])
		{
			$_POST=$this->post_trim($_POST);
			if($_POST['name']==""){
				$this->wapheader('index.php?c=addresume&',"姓名不能为空！");
			}
			if($_POST['sex']==""){
				$this->wapheader('index.php?c=addresume&',"性别不能为空！");
			}
			if($this->config['user_idcard']=="1"&&trim($_POST['idcard'])==""){
				$this->wapheader('index.php?c=addresume&',"身份证号码不能为空！");
			}
			if($_POST['living']==""){
				$this->wapheader('index.php?c=addresume&',"现居住地不能为空！");
			}
			unset($_POST['submit']);
			$this->obj->delfiledir("../upload/tel/".$this->uid);
			$where['uid']=$this->uid;
			$nid=$this->obj->update_once("resume",$_POST,$where);
			$this->obj->update_once("member",array('email'=>$_POST['email'],'moblie'=>$_POST['telphone']),$where);
			$nid?$this->wapheader('index.php?c=addresume&',"保存成功！"):$this->wapheader('index.php?c=addresume&',"保存失败！");
		}
	}
	function expect_action()
	{
		$eid=(int)$_POST['eid'];
		unset($_POST['submit']);
		unset($_POST['eid']);
		$_POST['name'] = iconv("utf-8", "gbk", $_POST['name']);
		$where['id']=$eid;
		$where['uid']=$this->uid;
		$_POST['lastupdate']=time();
		if($eid=="")
		{
			$num=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
			$maxnum=$this->config['user_number']-$num['resume_num'];
			$confignum=$this->config['user_number'];
			if($maxnum<=0 &&$confignum!="")
			{
				$this->wapheader('index.php?',"你的简历数已经超过系统设置的简历数了！");
			}

			$_POST['uid']=$this->uid;
			$nid=$this->obj->insert_into("resume_expect",$_POST);
			if ($nid)
			{
				$num=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
				if($num['resume_num']==0)
				{
					$this->obj->update_once('resume',array('def_job'=>$nid),array('uid'=>$this->uid));
				}
				$data['uid'] = $this->uid;
				$data['eid'] = $nid;
				$this->obj->insert_into("user_resume",$data);
				$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`+1","`uid`='".$this->uid."'");
				$state_content = "发布了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$nid\" target=\"_blank\">新简历</a>。";
				$fdata['uid']	  = $this->uid;
				$fdata['content'] = $state_content;
				$fdata['ctime']   = time();
				$this->obj->insert_into("friend_state",$fdata);
			}
			$eid=$nid;
		}else{
			$nid=$this->obj->update_once("resume_expect",$_POST,$where);
		}
		echo $nid;die;
	}
	function saveresume_action()
	{
		if($_POST['submit'])
		{
			if($_POST['eid']>0)
			{
				$table="resume_".$_POST['table'];
				$table_array=array("resume","resume_expect","resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume");
				if(!in_array($table,$table_array))
				{
					$this->wapheader('index.php?c=addresume&eid='.(int)$_POST['eid'].'&',"保存失败！");
				}
				$id=(int)$_POST['id'];
				$url=$_POST['table'];
				unset($_POST['submit']);
				unset($_POST['table']);
				unset($_POST['id']);
				if($_POST['syear'])
				{
					$_POST['sdate']=strtotime($_POST['syear']."-".$_POST['smouth']."-".$_POST['sday']);
					$_POST['edate']=strtotime($_POST['eyear']."-".$_POST['emouth']."-".$_POST['eday']);
					unset($_POST['syear']);
					unset($_POST['smouth']);
					unset($_POST['sday']);
					unset($_POST['eyear']);
					unset($_POST['emouth']);
					unset($_POST['eday']);
				}
				if($id)
				{
					$where['id']=$id;
					$where['uid']=$this->uid;
					$nid=$this->obj->update_once($table,$_POST,$where);
				}else{
					$_POST['uid']=$this->uid;
					$nid=$this->obj->insert_into($table,$_POST);
					$this->obj->DB_update_all("user_resume","`$url`=`$url`+1","`eid`='".(int)$_POST['eid']."' and `uid`='".$this->uid."'");
					$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".(int)$_POST['eid']."'");
					$this->obj->complete($resume_row);
				}
			}
			$nid?$this->wapheader('index.php?c=addresume&eid='.(int)$_POST['eid'].'&',"保存成功！"):$this->wapheader('index.php?c=addresume&eid='.(int)$_POST['eid'].'&',"保存失败！");
		}
	}
	function delresume_action()
	{
		$tables=array("skill","work","project","edu","training","cert","other");
		if(!in_array($_GET['type'],$tables))
		{
			$this->wapheader('index.php?c=addresume&eid='.$_GET['eid'].'&',"数据格式非法！");
		}
		$nid=$this->obj->DB_delete_all("resume_".$_GET['type'],"`eid`='".(int)$_GET['eid']."' and `id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
		if($nid)
		{
			$url=$_GET['type'];
			$this->obj->DB_update_all("user_resume","`$url`=`$url`-1","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".(int)$_GET['eid']."'");
			$this->obj->complete($resume_row);
			$this->wapheader('index.php?c=addresume&eid='.$_GET['eid'].'&',"删除成功！");
		}else{
			$this->wapheader('index.php?c=addresume&eid='.$_GET['eid'].'&',"删除失败！");
		}
	}
	function resume_action()
	{
		if($_GET['del'])
		{
			$del=(int)$_GET['del'];
			$del_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume");
			if($this->obj->DB_delete_all("resume_expect","`id`='".$del."' and `uid`='".$this->uid."'"))
			{
				foreach($del_array as $v)
				{
					$this->obj->DB_delete_all($v,"`eid`='".$del."'' and `uid`='".$this->uid."'","");
				}
				$def_id=$this->obj->DB_select_once("resume","`uid`='".$this->uid."' and `def_job`='".$del."'");
			    if(is_array($def_id))
			    {
					$row=$this->obj->DB_select_once("resume_expect","`uid`='".$this->uid."'");
					$this->obj->update_once('resume',array('def_job'=>$row['id']),array('uid'=>$this->uid));
			    }
				$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`-1","`uid`='".$this->uid."'");
				$this->wapheader("index.php?c=resume&","删除成功！");
			}else{
				$this->wapheader("index.php?c=resume&","删除失败！");
			}
		}
		if($_GET['update'])
		{
			$id=(int)$_GET['update'];
			$nid=$this->obj->update_once('resume_expect',array('lastupdate'=>time()),array('id'=>$id,'uid'=>$this->uid));
 			$nid?$this->wapheader("index.php?c=resume&","刷新成功！"):$this->wapheader("index.php?c=resume&","刷新失败！");
		}
		if($_POST['type']=="def_job")
		{
			$this->obj->DB_update_all("resume","`def_job`='".(int)$_POST['eid']."'","`uid`='".$this->uid."'");die;
		}
		$rows=$this->obj->DB_select_all("resume_expect","`uid`='".$this->uid."'","id,name,lastupdate,height_status,doc");
		$this->yunset("rows",$rows);
		$def_job=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","def_job");
		$this->yunset("def_job",$def_job);
		$this->waptpl('resume');
	}
	function loginout_action()
	{
		SetCookie("uid","",time() -86400, "/");
		SetCookie("username","",time() - 86400, "/");
		SetCookie("usertype","",time() -86400, "/");
		SetCookie("salt","",time() -86400, "/");
		SetCookie("shell","",time() -86400, "/");
		$this->wapheader('../index.php');
	}
}
?>