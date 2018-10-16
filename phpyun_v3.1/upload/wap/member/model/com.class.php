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
class com_controller extends common
{
	function waptpl($tpname)
	{
		$this->yuntpl(array('wap/member/com/'.$tpname));
	}
	function index_action()
	{
		$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$this->yunset("company",$company);
		$sqnum=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."'");
		$this->yunset("sqnum",$sqnum);
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
		$this->yunset("statis",$statis);
		$this->waptpl('index');
	}
	function com_action()
	{
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
		$this->yunset("statis",$statis);
		$this->waptpl('com');
	}
	function info_action()
	{
		if($_POST['submit'])
		{
			$_POST=$this->post_trim($_POST);
			if($_POST['name']=="")
			{
				$this->wapheader('index.php?c=info&',"企业全称不能为空！");
			}
			if($_POST['hy']=="")
			{
				$this->wapheader('index.php?c=info&',"从事行业不能为空！");
			}
			if($_POST['pr']=="")
			{
				$this->wapheader('index.php?c=info&',"企业性质不能为空！");
			}
			if($_POST['provinceid']=="")
			{
				$this->wapheader('index.php?c=info&',"企业地址不能为空！");
			}
			if($_POST['mun']=="")
			{
				$this->wapheader('index.php?c=info&',"企业规模不能为空！");
			}
			if($_POST['address']=="")
			{
				$this->wapheader('index.php?c=info&',"公司地址不能为空！");
			}
			if($_POST['linkphone']=="")
			{
				$this->wapheader('index.php?c=info&',"固定电话不能为空！");
			}
			if($_POST['linkmail']=="")
			{
				$this->wapheader('index.php?c=info&',"联系邮件不能为空！");
			}
			if($_POST['content']=="")
			{
				$this->wapheader('index.php?c=info&',"企业简介不能为空！");
			}
			$this->obj->delfiledir("../upload/tel/".$this->uid);
			unset($_POST['submitbtn']);
			$cert_email = $this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and `type`='1'");
			if(is_array($cert_email))
			{
				if($cert_email['check'] != $_POST['linkmail'])
				{
					$row['cert'] = str_replace(",1","",$row['cert']);
					$this->obj->DB_delete_all("company_cert","`id`='".$cert_email['id']."'");
				}
			}
			$cert_tel = $this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and `type`='2'");
			if(is_array($cert_tel))
			{
				if($cert_tel['check'] != $_POST['linktel'])
				{
					$row['cert'] = str_replace(",2","",$row['cert']);
					$this->obj->DB_delete_all("company_cert","`id`='".$cert_tel['id']."'");
				}
			}
			$_POST['cert'] = $row['cert'];
			$where['uid']=$this->uid;
			$_POST['lastupdate']=mktime();
			$nid=$this->obj->update_once("company",$_POST,$where);
			$data['com_name']=$_POST['name'];
			$data['pr']=$_POST['pr'];
			$data['mun']=$_POST['mun'];
			$data['com_provinceid']=$_POST['provinceid'];
			$this->obj->update_once("company_job",$data,array("uid"=>$this->uid));
			$this->obj->update_once("userid_job",array("com_name"=>$_POST['name']),array("com_id"=>$this->uid));
			$this->obj->update_once("fav_job",array("com_name"=>$_POST['name']),array("com_id"=>$this->uid));
			$this->obj->update_once("report",array("r_name"=>$_POST['name']),array("c_uid"=>$this->uid));
			$this->obj->update_once("blacklist",array("com_name"=>$_POST['name']),array("c_uid"=>$this->uid));
			$this->obj->update_once("msg",array("com_name"=>$_POST['name']),array("job_uid"=>$this->uid));
			$nid?$this->wapheader('index.php?c=info&',"更新成功！"):$this->wapheader('index.php?c=info&',"更新失败！");
		}
		$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$this->yunset("row",$row);
		$this->industry_cache();
		$this->com_cache();
		$this->city_cache();
		$this->waptpl('info');
	}
	function get_com($type)
	{
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
		if($statis['rating'])
		{
			if($type==1)
			{
				if($statis['vip_etime']<time())
				{
					if($statis['job_num'])
					{
						$value="`job_num`=`job_num`-1";
					}else{
						if($this->config['com_integral_online']=="1")
						{
							$this->intergal($type,$statis);
						}else{
							$this->wapheader('index.php?c=job&',"会员发布职位用完,购买会员服务更快捷！");
						}
					}
				}
			}elseif($type==2){
				if($statis['vip_etime']<time())
				{
					if($statis['editjob_num'])
					{
						$value="`editjob_num`=`editjob_num`-1";
					}else{
						if($this->config['com_integral_online']=="1")
						{
							$this->intergal($type,$statis);
						}else{
							$this->wapheader('index.php?c=job&',"会员修改职位用完！");
						}
					}
				}
			}
			if($value)
			{
				$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
			}
		}else{
			$this->intergal($type,$statis);
		}
	}
	function intergal($type,$statis){
		if($type==1 && $this->config['integral_job']){
			if($statis['integral']<$this->config['integral_job']){
				$this->wapheader('index.php?c=job&',"你的".$this->config['integral_pricename']."不够发布职位！");
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_job'],false,"发布职位");
		}elseif($type==2 && $this->config['integral_jobedit']){
			if($statis['integral']<$this->config['integral_jobedit'])
			{
				$this->wapheader('index.php?c=job&',"你的".$this->config['integral_pricename']."不够修改职位！");
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_jobedit'],false,"修改职位");
		}
	}
	function jobadd_action()
	{
		$rows=$this->obj->DB_select_all("company_cert","`uid`='".$this->uid."' group by type order by id desc");
		foreach($rows as $v)
		{
			$row[$v['type']]=$v;
		}
		$msg=array();
		$isallow_addjob="1";
		if($this->config['com_enforce_emailcert']=="1"){
			if($row['1']['status']!="1"){
				$isallow_addjob="0";
				$msg[]="邮箱认证";
			}
		}
		if($this->config['com_enforce_mobilecert']=="1"){
			if($row['2']['status']!="1"){
				$isallow_addjob="0";
				$msg[]="手机认证";
			}
		}
		if($this->config['com_enforce_licensecert']=="1"){
			if($row['3']['status']!="1"){
				$isallow_addjob="0";
				$msg[]="营业执照认证";
			}
		}
		if($this->config['com_enforce_setposition']=="1"){
			if(empty($company['x'])||empty($company['y'])){
				$isallow_addjob="0";
				$msg[]="设置企业地图";
				$url="index.php?c=map";
			}
		}
		if($isallow_addjob=="0"){
			$this->wapheader('index.php?c=job&',"请先登录电脑客户端完成".$this->pylode("、",$msg)."！");
		}
		if($_GET['id'])
		{
			$row=$this->obj->DB_select_once("company_job","`id`='".(int)$_GET['id']."'");
			if($row['lang']!="")
			{
				$row['lang']= @explode(",",$row['lang']);
			}
			if($row['welfare']!="")
			{
				$row['welfare']= @explode(",",$row['welfare']);
			}
			$row['days']= ceil(($row['edate']-$row['sdate'])/86400);
			$this->yunset("row",$row);
		}
		if($_POST['submit'])
		{
			$id=intval($_POST['id']);
			$state= intval($_POST['state']);
			unset($_POST['submit']);
			unset($_POST['id']);
			unset($_POST['state']);
			$_POST['uid']=$this->uid;
			$_POST['lastupdate']=mktime();
			$_POST['state']=$this->config['com_job_status'];
			if($this->config['com_job_status']=="0"){
				$msg="等待审核！";
			}
			if(!empty($_POST['lang']))
			{
				$_POST['lang'] = $this->pylode(",",$_POST['lang']);
			}else{
				$_POST['lang'] = "";
			}
			if(!empty($_POST['welfare']))
			{
				$_POST['welfare'] = $this->pylode(",",$_POST['welfare']);
			}else{
				$_POST['welfare'] = "";
			}
			$_POST['sdate']=time();
			$_POST['edate']=time()+$_POST['days']*86400;
			unset($_POST['days']);
			$com=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
			$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
			$_POST['com_name']=$com['name'];
			$_POST['com_logo']=$com['logo'];
			$_POST['com_provinceid']=$com['provinceid'];
			$_POST['pr']=$com['pr'];
			$_POST['mun']=$com['mun'];
			$_POST['rating']=$statis['rating'];
			$where['id']=$id;
			$where['uid']=$this->uid;
			if(!$id)
			{
				$this->get_com(1);
				$nid=$this->obj->insert_into("company_job",$_POST);
				$name="添加职位";
				if($nid)
				{
					if($this->config['com_job_status']=="0")
					{
						$sql ="`job`=`job`+1,`status0`=`status0`+1";
					}else{
						
						$sql="`job`=`job`+1,`status1`=`status1`+1";
					}
					$this->obj->DB_update_all("company_statis",$sql,"`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company","`jobtime`='".$_POST['lastupdate']."'","`uid`='".$this->uid."'");
					$state_content = "发布了新职位 <a href=\"".$this->config['sy_weburl']."/index.php?m=com&c=comapply&id=$nid\" target=\"_blank\">".$_POST['name']."</a>。";
					$this->addstate($state_content);
				}
			}else{
				if($state=="1" || $state=="2")
				{
					$this->get_com(2);
				}
				$rows=$this->obj->DB_select_once("company_job","`id`='".$id."'");
				$nid=$this->obj->update_once("company_job",$_POST,$where);
				$name="更新职位";
				if($nid)
				{
					if($rows['state']=="0")
					{
						if($this->config['com_job_status']=="1")
						{
							$value="`status0`=`status0`-1,`status1`=`status1`+1";
						}
					}elseif($rows['state']=="1"){
						if($this->config['com_job_status']=="0"){
							$value="`status0`=`status0`+1,`status1`=`status1`-1";
						}
					}elseif($rows['state']=="2"){
						if($this->config['com_job_status']=="0")
						{
							$value="`status0`=`status0`+1,`status2`=`status2`-1";
						}else{
							$value="`status1`=`status1`+1,`status2`=`status2`-1";
						}
					}else{
						if($this->config['com_job_status']=="0")
						{
							$value="`status0`=`status0`+1,`status3`=`status3`-1";
						}else{
							$value="`status1`=`status1`+1,`status3`=`status3`-1";
						}
					}
					$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company","`jobtime`='".$_POST['lastupdate']."'","`uid`='".$this->uid."'");
				}
			}
			$nid?$this->wapheader('index.php?c=job&',$name."成功！"):$this->wapheader('index.php?c=job&',$name."失败！");
		}
		$this->job_cache();
		$this->city_cache();
		$this->industry_cache();
		$this->com_cache();
		$this->waptpl('jobadd');
	}
	function job_action()
	{
		if($_GET['status'])
		{
			$this->obj->update_once('company_job',array('status'=>intval($_GET['status'])),array('id'=>intval($_GET['id'])));
			$this->wapheader('index.php?c=job&','设置成功！');
		}
		$urlarr=array("c"=>"job","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("company_job","`uid`='".$this->uid."'",$pageurl,"10");
		$this->waptpl('job');
	}
	function hr_action()
	{
		$urlarr=array("c"=>"hr","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("userid_job","`com_id`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows) && !empty($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
			}
			$userrows=$this->obj->DB_select_all("resume","`uid` in (".@implode(",",$uid).") and `r_status`<>'2'","`name`,`sex`,`edu`,`uid`");
			$yqlist=$this->obj->DB_select_all("userid_msg","`uid` in (".@implode(",",$uid).")","`uid`");
			if(is_array($userrows))
			{
				include(PLUS_PATH."user.cache.php");
				foreach($rows as $k=>$v)
				{
					foreach($userrows as $val)
					{
						if($v['uid']==$val['uid'])
						{
							$rows[$k]['name']=$val['name'];
							$rows[$k]['sex']=$userclass_name[$val['sex']];
							$rows[$k]['edu']=$userclass_name[$val['edu']];
						}
					}
					foreach($yqlist as $val)
					{
						if($v['uid']==$val['uid'])
						{
							$rows[$k]['yq']=1;
						}
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->waptpl('hr');
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
			$this->wapheader('../index.php','修改成功，请重新登录');
		}

		$this->waptpl('password');
	}

	function logout_action()
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