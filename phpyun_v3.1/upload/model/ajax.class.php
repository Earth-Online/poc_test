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
class ajax_controller extends common{
	function comindex_favjob_action()
	{
		if($_COOKIE['usertype']!=1)
		{
			echo 0;die;
		}else if(!$this->uid || !$this->username ){
			echo 4;die;
		}else{
			$jobid=@explode(",",$_POST['codewebarr']);
			if(is_array($jobid))
			{
				foreach($jobid as $v)
				{
					$rows=$this->obj->DB_select_all("fav_job","`uid`='".$this->uid."' and `job_id`='".(int)$v."' and `type`='".(int)$_POST['type']."'");
					if(is_array($rows)&&empty($rows))
					{
						$row=$this->obj->DB_select_alls("company_job","company","a.`uid`=b.`uid` and a.id='".(int)$v."' and a.`r_status`<>'2'","a.name as jobname,b.name,a.uid");
						$value['job_id']=$v;
						$value['com_name']=$row[0]['name'];
						$value['job_name']=$row[0]['jobname'];
						$value['com_id']=$row[0]['uid'];
						$value['uid']=$this->uid;
						$value['datetime']=mktime();
						$value['type']=(int)$_POST['type'];
						$nid=$this->obj->insert_into("fav_job",$value);
						if($nid)
						{
							$this->obj->DB_update_all("member_statis","`fav_jobnum`=`fav_jobnum`+1","uid='".$this->uid."'");
							$state_content = "我收藏了职位 <a href=\"".$this->config['sy_weburl']."/index.php?m=com&c=comapply&id=".$v."\">".$row[0]['jobname']."</a>";
							$this->addstate($state_content);
						}
					}
				}
			}
			echo 1;die;
		}
	}
	function comindex_sqjob_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1)
		{
			echo 0;die;
		}else{
			$jobid=@explode(",",$_POST['codewebarr']);
			if(is_array($jobid))
			{
				foreach($jobid as $v)
				{
					$rows=$this->obj->DB_select_once("userid_job","`uid`='".$this->uid."' and `job_id`='".(int)$v."'",'id');
					if(empty($rows))
					{
						$row=$this->obj->DB_select_alls("company_job","company","a.`uid`=b.`uid` and a.id='".(int)$v."' and a.`r_status`<>'2'","a.name as jobname,b.name,a.uid,a.sdate,a.edate");
						if(is_array($row) && $row[0]['sdate']<time() && $row[0]['edate']>time())
						{
							$value['job_id']=$v;
							$value['com_name']=$row[0]['name'];
							$value['job_name']=$row[0]['jobname'];
							$value['com_id']=$row[0]['uid'];
							$value['uid']=$this->uid;
							$value['eid']=$_POST['eid'];
							$value['datetime']=mktime();
							$nid=$this->obj->insert_into("userid_job",$value);
							if($nid)
							{
								$this->obj->DB_update_all("company_statis","`sq_job`=`sq_job`+1","uid='".$row[0]['uid']."'");
								$this->obj->DB_update_all("member_statis","`sq_jobnum`=`sq_jobnum`+1","uid='".$this->uid."'");
								$state_content = "我申请了职位 <a href=\"".$this->config['sy_weburl']."/index.php?m=com&c=comapply&id=".$v."\">".$row[0]['jobname']."</a>";
								$this->addstate($state_content);
								$this->sqjobmsg($v,$row[0]['uid']);
								$job_link=$this->obj->DB_select_once("company_job_link","`jobid`='".$v."' and `is_email`='1'");
								if($job_link['id']){
									$contents=file_get_contents($this->config[sy_weburl]."/index.php?m=resume&c=sendresume&job_link=1&id=".$_POST['eid']);
									$smtp = $this->email_set();
									$smtpusermail =$this->config['sy_smtpemail'];
									$myemail = iconv("UTF-8","GB2312//IGNORE",$this->config['sy_webname']);
									$sendid = $smtp->sendmail($job_link['email'],$smtpusermail,"您收到一份新的求职简历！——".$this->config['sy_webname'],$contents,"HTML","","","",$myemail);
								}
							}
						}
					}
				}
			}
			echo 1;die;
		}
	}
	function sq_job_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1)
		{
			echo 0;die;
		}
		$jobid=(int)$_POST['jobid'];
		$job=$this->obj->DB_select_once("company_job","`id`='".$jobid."' and `r_status`<>'2' and `status`<>'1'");
		if(is_array($job)&&!empty($job))
		{
			if($job['edate']<time())
			{
				echo 5;die;
			}
	        $isblackname=$this->obj->DB_select_once("blacklist","`p_uid`='".$this->uid."' and `c_uid`='".$job['uid']."' and `usertype`='2'");
	        if(is_array($isblackname))
	        {
	        	echo 4;die;
	        }
			$rows=$this->obj->DB_select_all("userid_job","`uid`='".$this->uid."' and `job_id`='".$jobid."'");
			if(is_array($rows)&&!empty($rows))
			{
				echo 3;die;
			}
			$value['job_id']=$jobid;
			$value['com_name']=iconv("utf-8","gbk",$_POST['companyname']);
			$value['job_name']=iconv("utf-8","gbk",$_POST['jobname']);
			$value['com_id']=(int)$_POST['companyuid'];
			$value['uid']=$this->uid;
			$value['eid']=(int)$_POST['eid'];
			$value['datetime']=mktime();
			$nid=$this->obj->insert_into("userid_job",$value);
			if($nid)
			{
				$this->obj->DB_update_all("company_statis","`sq_job`=`sq_job`+1","uid='".$value['com_id']."'");
				$this->obj->DB_update_all("member_statis","`sq_jobnum`=`sq_jobnum`+1","uid='".$this->uid."'");
				$this->sqjobmsg($jobid,$value['com_id']);
				$job_link=$this->obj->DB_select_once("company_job_link","`jobid`='".$jobid."' and `is_email`='1'");
				if($job_link['id']){
					$contents=file_get_contents($this->config[sy_weburl]."/index.php?m=resume&c=sendresume&job_link=1&id=".$_POST['eid']);
					$smtp = $this->email_set();
					$smtpusermail =$this->config['sy_smtpemail'];
					$myemail = iconv("UTF-8","GB2312//IGNORE",$this->config['sy_webname']);
					$sendid = $smtp->sendmail($job_link['email'],$smtpusermail,"您收到一份新的求职简历！——".$this->config['sy_webname'],$contents,"HTML","","","",$myemail);
				}
			}
			echo $nid?1:2;die;
		}else{
			echo 6;die;
		}
	}
	function fav_job_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1)
		{
			echo 0;die;
		}else{
			$rows=$this->obj->DB_select_all("fav_job","`uid`='".$this->uid."' and `job_id`='".(int)$_POST['jobid']."'");
			if(is_array($rows)&&!empty($rows))
			{
				echo 3;die;
			}
			$value['job_id']=(int)$_POST['jobid'];
			$value['com_name']=iconv("utf-8","gbk",$_POST['companyname']);
			$value['job_name']=iconv("utf-8","gbk",$_POST['jobname']);
			$value['com_id']=$_POST["companyuid"];
			$value['uid']=$this->uid;
			$value['datetime']=mktime();
			$nid=$this->obj->insert_into("fav_job",$value);
			if($nid){
				$this->obj->DB_update_all("member_statis","`fav_jobnum`=`fav_jobnum`+1","uid='".$this->uid."'");
			}
			echo $nid?1:2;die;
		}
	}
	function favjobuser_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1){
			echo 0;die;
		}
		if($_COOKIE['usertype']!=1){
			echo 4;die;
		}
		$rows=$this->obj->DB_select_all("fav_job","`uid`='".$this->uid."' and `job_id`='".(int)$_POST['id']."'");
		if(is_array($rows)&&!empty($rows)){
			echo 3;die;
		}
		$job=$this->obj->DB_select_once("company_job","`id`='".(int)$_POST['id']."'");
		$data['job_id']=$job['id'];
		$data['com_name']=$job['com_name'];
		$data['job_name']=$job['name'];
		$data['com_id']=$job['uid'];
		$data['uid']=$this->uid;
		$data['datetime']=time();
		$nid=$this->obj->insert_into("fav_job",$data);
		if($nid){
			$this->obj->DB_update_all("member_statis","`fav_jobnum`=`fav_jobnum`+1","uid='".$this->uid."'");
		}
		echo $nid?1:2;die;
	}
	function index_ajaxjob_action()
	{
		$jobid=@explode(",",$_POST['jobid']);
		if(is_array($jobid))
		{
			foreach($jobid as $value)
			{
				if(intval($value)>0)
				{
					$job_ids[] =intval($value);
				}
			}
			$jobid = @implode(",",$job_ids);
		}
		if(!$jobid)
		{
			$jobid=0;
		}
		$company_id=$this->obj->DB_select_all("company_job","`id` in (".$jobid.") and `r_status`<>'2' and `status`<>'1'","uid");
		if(!empty($company_id))
		{
			$com_id=array();
			foreach($company_id as $k=>$v)
			{
				if(in_array($v['uid'],$com_id)==false){
					$com_id[]=$v['uid'];
				}
			}
			$com_ids=implode(',',$com_id);
		}
        $isblackname=$this->obj->DB_select_all("blacklist","`p_uid`='".(int)$_POST['p_uid']."' and `c_uid` in (".$com_ids.") and `usertype`='2'");
        if(!empty($isblackname) && strstr($jobid,',')==false){
        	echo 4;die;
        }
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1){
			echo 0;die;
		}else{
			$rows=$this->obj->DB_select_all("userid_job","`uid`='".$this->uid."' and `job_id`='$jobid'");
			if(is_array($rows)&&!empty($rows) && strstr($jobid,',')==false){
				echo 3;die;
			}
			$rows=$this->obj->DB_select_all("resume_expect","`uid`='".$this->uid."'");
			$def_job=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","def_job");
			if(is_array($rows)&&!empty($rows))
			{
				foreach($rows as $v)
				{
					if($def_job['def_job']==$v['id']){
						$data.='<em><input type="radio" name="resume" value="'.$v['id'].'" id="resume_'.$v['id'].'" checked/><label for="resume_'.$v['id'].'">'.$v['name'].'</label></em>';
					}else{
						$data.='<em><input type="radio" name="resume" value="'.$v['id'].'" id="resume_'.$v['id'].'"/><label for="resume_'.$v['id'].'">'.$v['name'].'</label></em>';
					}
				}
				echo $data;
			}else{
				echo 2;die;
			}
		}
	}
	function index_ajaxresume_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=2)
		{
			$arr['status']=0;
		}else{
			if($_POST['show_job'])
			{
				$company_job=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."' and `state`='1'","`name`,`id`");
				if($company_job&&is_array($company_job))
				{
					foreach($company_job as $val)
					{
						$html.="<option value='".iconv("gbk","utf-8",$val['name'])."'>".iconv("gbk","utf-8",$val['name'])."</option>";
					}
					$arr['html']=$html;
				}else{
					$arr['status']=5;
				}
			}
			if($arr['status']=='')
			{
				$company_yq=$this->obj->DB_select_once("userid_msg","`fid`='".$this->uid."' and `default`='1' order by `id` desc");
				if($company_yq['content']){
					$arr['content']=iconv("gbk","utf-8",$company_yq['content']);
				}else{
					$content="面试时间：\n公司地址：\n";
					$arr["content"]=iconv("gbk","utf-8",$content);
				}
				$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
				if($row['rating']==0)
				{
					$arr['status']=1;
					$arr['integral']=$this->config['integral_interview'];
				}else{
					if($row['vip_etime']>time())
					{
						$arr['status']=3;
					}elseif($row['invite_resume']==0){
						if($this->config['com_integral_online']=="1")
						{
							$arr['status']=2;
							$arr['integral']=$this->config['integral_interview'];
						}else{
							$arr['status']=4;
						}
					}else{
						$arr['status']=3;
					}
				}
			}
		}
		echo json_encode($arr);die;
	}
	function sava_ajaxresume_action()
	{
		$data['uid']=(int)$_POST['uid'];
		$data['title']='面试邀请';
		$data['content']=iconv("utf-8","gbk",$_POST['content']);
		$data['fid']=$this->uid;
		$data['datetime']=time();
		$info['content']=$data['content'];
		$info['jobname']=iconv("utf-8","gbk",$_POST['jobname']);
		$info['username']=iconv("utf-8","gbk",$_POST['username']);
        $p_uid=(int)$_POST['uid'];
		$black=$this->obj->DB_select_once("blacklist","`p_uid`='".$p_uid."' and `c_uid`='".$this->uid."'");
		if(!empty($black))
		{
			$arr['status']=8;
			echo json_encode($arr);die;
		}
		$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$p_uid."' and `p_uid`='".$this->uid."'");
		if(!empty($black))
		{
			$arr['status']=9;
			echo json_encode($arr);die;
		}
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=2)
		{
			$arr['status']=0;
			echo json_encode($arr);die;
		}else{
			$umessage = $this->obj->DB_select_once("userid_msg","`uid`='".$p_uid."' AND `fid`='".$this->uid."'");
			if(is_array($umessage))
			{
			 	$arr['status']=7;
			}else{
				$com=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","name");
				$resume=$this->obj->DB_select_once("resume","`uid`='".$p_uid."'","name");
				$data['fname']=$com['name'];
				if($_POST['update_yq']=='1')
				{
					$data['default']=1;
				}else{
					$this->obj->DB_update_all("userid_msg","`default`='0'","`fid`='".$this->uid."'");
				}
				$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
				if($row['rating']==0)
				{
					if($row['integral']<$this->config['integral_interview'])
					{
						$arr['status']=5;
						$arr['integral']=$row["integral"];
					}else{
						$this->obj->insert_into("userid_msg",$data);
						$this->obj->company_invtal($this->uid,$this->config['integral_interview'],false,"邀请会员面试",true,2,'integral',14);
						$this->msg_post($_POST['uid'],$this->uid,$info);
						$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=".$_POST[eid]."\" target=\"_blank\">".$resume['name']."</a> 面试。";
						$this->addstate($state_content);
						$arr['status']=3;
					}
				}else{
					if($row['vip_etime']>time())
					{
						$this->obj->insert_into("userid_msg",$data);
						$this->msg_post($_POST['uid'],$this->uid,$info);
						$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".$resume['name']."</a> 面试。";
						$this->addstate($state_content);
						$arr['status']=3;
					}elseif($row["invite_resume"]==0){
						if($row["integral"]<$this->config["integral_interview"])
						{
							$arr['status']=5;
							$arr['integral']=$row['integral'];
						}else{
							$this->obj->insert_into("userid_msg",$data);
							$this->obj->company_invtal($this->uid,$this->config['integral_interview'],false,"邀请会员面试",true,2,'integral',14);
							$this->msg_post($_POST['uid'],$this->uid,$info);
							$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".$resume['name']."</a> 面试。";
							$this->addstate($state_content);
							$arr['status']=3;
						}
					}else{
						$this->obj->insert_into("userid_msg",$data);
						$this->obj->DB_update_all("company_statis","`invite_resume`=`invite_resume`-1","uid='".$this->uid."'");
						$this->msg_post($_POST['uid'],$this->uid,$info);
						$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".$resume['name']."</a> 面试。";
						$this->addstate($state_content);
						$arr['status']=3;
					}
				}
			}
		}
		echo json_encode($arr);
	}
	function msg_post($uid,$comid,$row=''){
		$com=$this->obj->DB_select_once("company","`uid`='$comid'");
		$uid=$this->obj->DB_select_once("member","`uid`='$uid'","email,moblie");
		$data['type']="yqms";
		$data['company']=$com['name'];
		$data['linkman']=$com['linkman'];
		$data['comtel']=$com['linktel'];
		$data['comemail']=$com['linkmail'];
		$data['content']=@str_replace("\n","<br/>",$row['content']);
		$data['jobname']=$row['jobname'];
		$data['username']=$row['username'];

		if($this->config['sy_msg_type']=="0"){
			$row=$this->obj->DB_select_once("company_statis","`uid`='".$comid."'","msg_num");

			if($row['msg_num']>0){
				$data['moblie']=$uid['moblie'];
				$msg=$this->send_msg_email($data);
				unset($data['moblie']);
				if($msg=="发送成功!"){
					$this->obj->DB_update_all("company_statis","`msg_num`=`msg_num`-1","`uid`='".$comid."'");
				}
			}
			$data['email']=$uid['email'];
			$this->send_msg_email($data);
		}else{
			$data['email']=$uid['email'];
			$data['moblie']=$uid['moblie'];
			$this->send_msg_email($data);
		}
	}
	function paypost_action(){
		$dingdan=(int)$_GET['dingdan'];
	    $row=$this->obj->DB_select_once("company_order","`order_id`='".$dingdan."'","uid,order_price");
		$uid=$this->obj->DB_select_once("member","`uid`='".$row['uid']."'","email,moblie");
		$data['type']="fkcg";
		$data['email']=$uid['email'];
		$data['moblie']=$uid['moblie'];
		$data['order_id']=$dingdan;
		$data['price']=$row['order_price'];
		$data['date']=date("Y-m-d");
		$this->send_msg_email($data);
	}
	function for_link_action()
	{
		$eid=(int)$_POST['eid'];
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=2)
		{
			$arr['status']=1;
			$arr['msg']="您未登录，或非企业账户！";
		}else{
			$resume=$this->obj->DB_select_once("down_resume","`eid`='".$eid."' and `comid`='".$this->uid."'");
			$user=$this->obj->DB_select_alls("resume","resume_expect","a.`r_status`<>'2' and a.`uid`=b.`uid` and b.`id`='".$eid."'","a.name,a.email,a.uid,b.id");
			$user=$user[0];
			$black=$this->obj->DB_select_once("blacklist","`p_uid`='".$user['uid']."' and `c_uid`='".$this->uid."'");
			if(!empty($black))
			{
				$arr['status']=1;
				$arr['msg']="该用户已被您列入黑名单！";
			}
			$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$user['uid']."' and `p_uid`='".$this->uid."'");
			if(!empty($black))
			{
				$arr['status']=1;
				$arr['msg']="您已被该用户列入黑名单！";
			}
			if(is_array($resume)&&$arr['status']=='')
			{
				$arr['status']=3;
			}else if($arr['status']==''){
				$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","vip_etime,down_resume");
				$data['eid']=$user['id'];
				$data['uid']=$user['uid'];
				$data['comid']=$this->uid;
				$data['downtime']=time();
				if($row["vip_etime"]>time())
				{
					$this->obj->insert_into("down_resume",$data);
					$state_content = "新下载了简历 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$user[id]\" target=\"_blank\">".$user['name']."</a> 。";
					$this->addstate($state_content);
					$arr['status']=3;
				}elseif($row["down_resume"]==0){
					if($this->config['com_integral_online']=="1")
					{
						$arr['status']=2;
						$arr['uid']=$user['uid'];
						$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_down_resume'].$this->config['integral_pricename']."，是否下载？";
					}else{
						$arr['status']=1;
						$arr['msg']="会员下载简历已用完！";
					}
				}else{
					$this->obj->insert_into("down_resume",$data);
					$this->obj->DB_update_all("company_statis","`down_resume`=`down_resume`-1","uid='".$this->uid."'");
					$state_content = "新下载了简历 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$user[id]\" target=\"_blank\">".$user['name']."</a> 。";
					$this->addstate($state_content);
					$arr['status']=3;
				}
			}
			if($arr['status']==3)
			{
				$telphone=APP_PATH."upload/tel/".$user['uid']."/telphone.gif";
				$telhome=APP_PATH."upload/tel/".$user['uid']."/telhome.gif";
				$html="<table>";
				if(file_exists($telphone))
				{
					$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","手机：")."</td><td><img src=\"".$this->config['sy_weburl']."/upload/tel/".$user['uid']."/telphone.gif\"></td></tr>";
				}
				if(file_exists($telhome)&&$user['basic_info']=='1')
				{
					$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","座机：")."</td><td><img src=\"".$this->config['sy_weburl']."/upload/tel/".$user['uid']."/telhome.gif\"></td></tr>";
				}
				$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","邮箱：")."</td><td>".$user['email']."</td></tr>";
				$html.="</table>";
				$arr['html']=$html;
			}
		}
		$arr['msg']=iconv("gbk", "utf-8",$arr['msg']);
		echo json_encode($arr);die;
	}
	function down_resume_action()
	{
		$eid=(int)$_POST['eid'];
		$uid=(int)$_POST['uid'];
		$type=$_POST['type'];
		$data['eid']=$eid;
		$data['uid']=$uid;
		$data['comid']=$this->uid;
		$data['downtime']=time();
		if(!$this->uid || !$this->username || $_COOKIE["usertype"]!=2)
		{
			$arr['status']=0;
			echo json_encode($arr);
			die;
		}else{
			$black=$this->obj->DB_select_once("blacklist","`p_uid`='".$uid."' and `c_uid`='".$this->uid."'");
			if(!empty($black)){
				$arr['status']=7;
				echo json_encode($arr);die;
			}
			$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$uid."' and `p_uid`='".$this->uid."'");
			if(!empty($black)){
				$arr['status']=8;
				echo json_encode($arr);die;
			}
			$resume=$this->obj->DB_select_once("down_resume","`eid`='$eid' and `comid`='".$this->uid."'");
			if(is_array($resume)){
				$arr['status']=6;
				echo json_encode($arr);die;
			}
			$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");

			if($type=="integral"){
				if($row["integral"]<$this->config["integral_down_resume"]){
					$arr['status']=5;
					$arr['integral']=$row["integral"];
				}else{
					$this->obj->insert_into("down_resume",$data);
					$this->obj->company_invtal($this->uid,$this->config["integral_down_resume"],false,"下载简历",true,2,'integral',13);
					$state_content = "新下载了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".iconv("utf-8", "gbk",$_POST['username'])."</a> 的简历。";
					$this->addstate($state_content);
					$arr['status']=3;
				}
			}else{
				$arr['integral']=$this->config["integral_down_resume"];
				if($row[rating]==0){
					$arr['status']=1;
				}else{
					if($row["vip_etime"]>time()){
						$this->obj->insert_into("down_resume",$data);
						$state_content = "新下载了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".iconv("utf-8", "gbk",$_POST['username'])."</a> 的简历。";
						$this->addstate($state_content);
						$arr['status']=3;
					}elseif($row["down_resume"]==0){
						if($this->config['com_integral_online']=="1"){
							$arr['status']=2;
						}else{
							$arr['status']=4;
						}
					}else{
						$this->obj->insert_into("down_resume",$data);
						$this->obj->DB_update_all("company_statis","`down_resume`=`down_resume`-1","uid='".$this->uid."'");
						$state_content = "新下载了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".iconv("utf-8", "gbk",$_POST['username'])."</a> 的简历。";
						$this->addstate($state_content);
						$arr['status']=3;
					}
				}
			}
		}

		echo json_encode($arr);
	}

	function default_resume_action(){
		$eid=(int)$_POST['eid'];
		if(!$this->uid || !$this->username || $_COOKIE["usertype"]!=1){
			echo 0;die;
		}else{
			$nid=$this->obj->DB_update_all("resume","`def_job`='$eid'","`uid`='".$this->uid."'");
			echo $nid?1:2;
		}
	}

	function order_type_action(){
		if($this->uid  && $this->username && $_COOKIE['usertype']==2)
		{
			$nid=$this->obj->DB_update_all("company_order","`order_type`='".(int)$_POST['paytype']."'","`order_id`='".(int)$_POST['order']."'");
			echo $nid?1:2;
		}
	}
	function getmake_action(){
		$this->web_config();
	}
	function ajax_action(){
		include(PLUS_PATH."city.cache.php");
		if(is_array($_POST['str'])){
			$cityid=$_POST['str'][0];
		}else{$cityid=$_POST['str'];}

		$data="<option value=''>--请选择--</option>";
		if(is_array($city_type[$cityid])){
			foreach($city_type[$cityid] as $v){
				$data.="<option value='$v'>".$city_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function showcity_action(){
		include APP_PATH."/plus/city.cache.php";
		$city = $this->city_info($city_type[$_POST['id']],$city_name);
		if(is_array($city))
		{
			if($_POST['head']!="1")
			{
				$cityid = "city2";
			}else{

				$cityid="city2_head";
			}
			$html.="<dl class=\"city_4\">";

			foreach($city as $k=>$v)
			{
					$html.="<input type=\"radio\" id=\"".$v['id']."\" value=\"".$v['name']."+".$v['id']."\" name=\"cityid\"><dd><a href=\"javascript:void();\" onclick=\"showdetails(".$v['id'].",'".$cityid."','ajax','showcity');\"><label for=\"".$v['id']."\">".$v['name']."</label></a></dd>";
			}
			$html.="</dl>";
		}
		echo $html;
	}
	function showhy_action(){
		include APP_PATH."/plus/job.cache.php";
		$hy = $this->city_info($job_type[$_POST['id']],$job_name);
		if(is_array($hy))
		{
			$check_arr = @explode(",",$_POST['check_val']);
			foreach($hy as $k=>$v)
			{
				if(is_array($check_arr))
				{
					$checked="";
					if(in_array($v[id],$check_arr)){
						$checked = "checked";
					}
				}
				$html.=" <li><input id=\"".$v['id']."\" ".$checked." type=\"checkbox\"  onclick=\"checked_input('".$v['id']."','hy_box','check_hy');\" value=\"".$v['id']."+".$v['name']."\"><a href=\"javascript:void();\" >".$v['name']."</a></li>";
			}
		}
		echo $html;
	}

	function showjob_action(){
		include APP_PATH."/plus/job.cache.php";
		$job = $this->city_info($job_type[$_POST['id']],$job_name);
		if(is_array($job))
		{
			$check_arr = @explode(",",$_POST['check_val']);
			foreach($job as $k=>$v)
			{
				if(is_array($check_arr))
				{
					$checked="";
					if(in_array($v['id'],$check_arr)){
						$checked = "checked";
					}
				}
				if($_POST['divid']=="job3")
				{
					$html.=" <li><input class=\"del$id\" id=\"".$v['id']."\" ".$checked." onclick=\"checked_input('".$v['id']."','job_box','check_job','$id');\" type=\"checkbox\" value=\"".$v['id']."+".$v['name']."\"><a href=\"javascript:void();\">".$v['name']."</a></li>";
				}else{
					$html.=" <li><input id=\"".$v[id]."\" ".$checked." onclick=\"checked_input('".$v[id]."','job_box','check_job','".$v[id]."');\" type=\"checkbox\" value=\"".$v[id]."+".$v[name]."\"><a href=\"javascript:void();\" onclick=\"showdetails(".$v[id].",'".$_POST['divid2']."','ajax','showjob');\">".$v[name]."</a></li>";
				}
			}
		}
		echo $html;
	}
	function ajax_job_action(){
		include(PLUS_PATH."job.cache.php");
		if(is_array($_POST[str])){
			$jobid=$_POST[str][0];
		}else{
			$jobid=$_POST[str];
		}
		$data="<option value=''>--请选择--</option>";
		if(is_array($job_type[$jobid])){
			foreach($job_type[$jobid] as $v){
				$data.="<option value='$v'>".$job_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function ajax_ltjob_action(){
		include(PLUS_PATH."ltjob.cache.php");
		$jobid=$_POST['str'];
		$data="<option value=''>--请选择--</option>";
		if(is_array($ltjob_type[$jobid])){
			foreach($ltjob_type[$jobid] as $v){
				$data.="<option value='$v'>".$ltjob_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function ajax_ltjobone_action(){
		include(PLUS_PATH."ltjob.cache.php");
		$jobid=$_POST['str'];
		$data="";
		if(is_array($ltjob_type[$jobid])){
			foreach($ltjob_type[$jobid] as $v){
				$data.="<li> <a onclick=\"selectjobtwo('".$v."','jobtwo','".$ltjob_name[$v]."','jobtype');\" href=\"javascript:;\"> ".$ltjob_name[$v]."</a> </li>";
			}
		}
		echo $data;
	}
	function delupload_action(){
		if(!$this->uid || !$this->username || $_COOKIE["usertype"]!=2){
			echo 0;die;
		}else{
			$dir=$_POST[str][0];
			$isuser = $this->obj->DB_select_once("company_show","`picurl`='$dir'");
			if($isuser['uid']==$this->uid)
			{
				echo @unlink(".".$dir);
			}else{
				echo 0;die;
			}
		}
	}
	function emailcert_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']==1)
		{
			echo 0;die;
		}else{
			if($this->config['sy_smtpserver']=="" || $this->config['sy_smtpemail']=="" || $this->config['sy_smtpuser']==""){
				echo 3;die;
			}
			if($this->config['sy_email_cert']=="2"){
				echo 2;die;
			}
			$email=$_POST[str][0];
			$randstr=rand(10000000,99999999);
			$sql['status']=0;
			$sql['step']=1;
			$sql['check']=$email;
			$sql['check2']=$randstr;
			$sql['ctime']=mktime();
			$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='1'");
			if(is_array($row))
			{
				$where['uid']=$this->uid;
				$where['type']='1';
				$this->obj->update_once("company_cert",$sql,$where);
			}else{
				$sql['uid']=$this->uid;
				$sql['type']=1;
				$this->obj->insert_into("company_cert",$sql);
			}
			$base=base64_encode($this->uid."|".$randstr."|".$this->config['coding']);
			$data['type']="cert";
			$data['email']=$email;
			if($_COOKIE['usertype']=="2"){
				$data['url']="<a href='".$this->config['sy_weburl']."/index.php?m=qqconnect&c=cert&id=".$base."'>点击认证</a>";
			}elseif($_COOKIE['usertype']=="3"){
				$data['url']="<a href='".$this->config['sy_weburl']."/index.php?m=qqconnect&c=cert&type=3&id=".$base."'>点击认证</a>";
			}
			$data['date']=date("Y-m-d");
			$this->send_msg_email($data);
			echo "1";die;
		}
	}
	function mobliecert_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']==1)
		{
			echo 0;die;
		}else{
			$shell=$this->obj->GET_user_shell($this->uid,$_COOKIE['shell']);
			if(!is_array($shell)){echo 2;die;}
			$moblie=$_POST[str][0];
			$randstr=rand(100000,999999);
			setcookie("moblie_code",$randstr,time()+120, "/");
			$sql['status']=0;
			$sql['step']=1;
			$sql['check']=$moblie;
			$sql['check2']=$randstr;
			$sql['ctime']=mktime();
			$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='2'");
			if(is_array($row))
			{
				$where['uid']=$this->uid;
				$where['type']='2';
				$this->obj->update_once("company_cert",$sql,$where);
			}else{
				$sql['uid']=$this->uid;
				$sql['type']=2;
				$this->obj->insert_into("company_cert",$sql);
			}
			if($this->config['sy_msg_online']=="0" || $this->config['sy_msg_cert']=="2")
			{
				echo 3;die;
			}else{
				$data['type']="cert";
				$data['moblie']=$moblie;
				$data['code']=$randstr;
				$data['date']=date("Y-m-d");
				$this->send_msg_email($data);
				echo "1";die;
			}
		}
	}
    function mobliecode_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']==1){
			echo 0;die;
		}else{
			$code=$_POST[str][0];
			$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and `check2`='".$code."'");
			if(is_array($row) && $_COOKIE['moblie_code']==$code)
			{
				if($row['status']!=1)
				{
					$value.="`cert`=concat(`cert`,',2'),";
				}
				$value.="`linktel`='".$row['check']."'";
				$this->obj->DB_update_all("company",$value,"`uid`='".$this->uid."'");
				$this->obj->DB_update_all("member","`moblie`='".$row['check']."'","`uid`='".$this->uid."'");
				$id=$this->obj->DB_update_all("company_cert","`status`='1'","`uid`='".$this->uid."' and `check2`='".$code."'");
				echo $id?1:3;die;
			}else{
				echo 2;die;
			}
		}
    }
    function mapconfig_action(){
    	$arr=array();
		$arr['map_x']=$this->config['map_x'];
    	$arr['map_y']=$this->config['map_y'];
    	$arr['map_rating']=$this->config['map_rating'];
    	$arr['map_control']=$this->config['map_control'];
    	$arr['map_control_anchor']=$this->config['map_control_anchor'];
    	$arr['map_control_type']=$this->config['map_control_type'];
    	$arr['map_control_xb']=$this->config['map_control_xb'];
    	$arr['map_control_scale']=$this->config['map_control_scale'];
    	echo json_encode($arr);
    }
    function getzph_action()
    {
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=2)
		{
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","您不是企业用户或者还没有登录,<a href='index.php?m=login&usertype=2'>请先登录</a>");
		}else{
			$row=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."' and `state`='1' and `r_status`<>'2' and `status`<>'1'");
			if(is_array($row)&&$row)
			{
				foreach($row as $v)
				{
					$data.='<input type="checkbox" name="checkbox_job" value="'.$v['id'].'" id="status_'.$v['id'].'"><label for="status_'.$v['id'].'">'.$v['name'].'</label><br>';
				}
				$arr['status']=1;
				$arr['uid']=$this->uid;
				$arr['content']=iconv("gbk","utf-8",$data);
			}else{
				$arr['status']=2;
				$arr['content']=iconv("gbk","utf-8","您还没有有效职位，<a href='member/index.php?c=jobadd'>请先添加职位</a>");

			}
		}
		echo json_encode($arr);
	}
	function zphcom_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=2 || $this->uid!=$_GET['uid'])
		{
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","您不是企业用户或者还没有登录,<a href='index.php?m=login&usertype=2'>请先登录</a>");
		}elseif(!$_GET['pid']){
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","你没有选择招聘会");
		}elseif(!$_GET['jobid']){
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","你还没有选择职位");
		}elseif(is_array($this->obj->DB_select_once("zhaopinhui_com","uid='".(int)$_GET['uid']."' and zid='".(int)$_GET['pid']."'"))){
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","您已经参与该招聘会");
		}else{
			$jobidarr=@explode(",",$_GET['jobid']);
			$array=array();
			foreach($jobidarr as $v){
				if(!in_array($v,$array)){
					$array[]=$v;
				}
			}
			$sql['uid']=$_GET['uid'];
			$sql['zid']=$_GET['pid'];
			$sql['jobid']=@implode(",",$array);
			$sql['ctime']=mktime();
			$sql['status']=0;
			$id=$this->obj->insert_into("zhaopinhui_com",$sql);
			if($id){
				$arr['status']=1;
				$arr['content']=iconv("gbk","utf-8","报名成功,等级管理员审核");
			}else{
				$arr['status']=0;
				$arr['content']=iconv("gbk","utf-8","报名失败,请稍后重试");
			}
		}
		echo json_encode($arr);
	}
	function getzphcom_action()
	{
		if(!$_GET['jobid'])
		{
			$arr['status']=0;
			$arr['content']=iconv("gbk","utf-8","您还没有职位，<a href='index.php?m=login&usertype=2'>请先登录</a>");
		}else{
			$jobid=(int)$_GET['jobid'];
			$row=$this->obj->DB_select_all("company_job","`id`='".$jobid."'  and `uid`='".$this->uid."' and `r_status`<>'2' and `status`<>'1'");
			$zhaopinhui=$this->obj->DB_select_once("zhaopinhui","`id`='".$_GET['zid']."'","`title`,`address`,`starttime`,`endtime`");
			foreach($row as $v)
			{
				$data[]=$v['name'];
			}
			$cname=@implode('、',$data);
			$arr['status']=1;
			$arr['content']=iconv("gbk","utf-8",$cname);
			$arr['title']=iconv("gbk","utf-8",$zhaopinhui['title']);
			$arr['address']=iconv("gbk","utf-8",$zhaopinhui['address']);
			$arr['starttime']=iconv("gbk","utf-8",$zhaopinhui['starttime']);
			$arr['endtime']=iconv("gbk","utf-8",$zhaopinhui['endtime']);
		}
		echo json_encode($arr);
	}
    function job_check_action()
    {
    	if((int)$_POST['id'])
    	{
			if($this->config['sy_web_site']=="1")
			{
				if($_SESSION['cityid']>0)
				{
					$sitesql = "AND `cityid`='".$_SESSION['cityid']."'";
				}
			}
			$time = time();
			$limit = intval($_POST['limit']);
			$com=$this->obj->DB_select_all("company","`hy`='".(int)$_POST['id']."' and `r_status`<>'2' $sitesql order by `jobtime` desc limit $limit");
			if(is_array($com))
			{
				foreach($com as $key=>$val)
				{
					$arruid[]=$val['uid'];
				}
				$arruid=@implode(",",$arruid);
			}
			$job_list = $this->obj->DB_select_all("company_job","`uid` in (".$arruid.") and status<>'1' and r_status<>'2' and `state`='1' ORDER BY `lastupdate` DESC");
			if(is_array($com))
			{
				foreach($com as $k=>$v)
				{
					foreach($job_list as $val)
					{
						if($v['uid']==$val['uid'] && $v['jobtime']==$val['lastupdate'])
						{
								$comname = mb_substr($v['name'],0,12,"gbk");
								$time  = date("m-d",$val['lastupdate']);
								$joburl = $this->url("index","com",array("c"=>"comapply","id"=>$val['id']),"1");
								$comurl = $this->curl(array("url"=>"id:".$v['uid']));
								$jobname = mb_substr($val['name'],0,12,"gbk");
								if($jobname=="")
								{
									$jobname_div="<span>".$jobname."<font color=\"red\">&nbsp;该职位正在审核中</font></span>";
								}else{
									$jobname_div="<a href=\"$joburl\" target=\"_blank\" style=\"color:#4d4d4d;\">$jobname</a>";
								}
								$html.="<li><a href=\"$comurl\" target=\"_blank\"  class=\"news_na\">$comname</a><em>($time)</em><div class=\"clear\"></div><span style=\"float:left;\">聘：</span>$jobname_div</li>";
						}
					}
				}
			}
			echo $html;die;
		}
    }
    function resume_word_action()
    {
		$resumename=$this->obj->DB_select_once("resume","`def_job`='".(int)$_GET['id']."'","`name`");
		$resume=$this->obj->DB_select_once("down_resume","`eid`='".(int)$_GET['id']."' and `comid`='".$this->uid."'");
		if(is_array($resume) && !empty($resume))
		{
			$content = file_get_contents($this->config['sy_weburl']."/index.php?m=resume&id=".(int)$_GET['id']."&downtime=".$resume['downtime']."&type=word");
			$this->startword($resumename['name'],$content);
		}
	}
	 function startword($wordname,$html)
    {
        ob_start();
     	header("Content-Type:  application/msword");
		header("Content-Disposition:  attachment;  filename=".$wordname.".doc");
		header("Pragma:  no-cache");
		header("Expires:  0");
		echo $html;
    }
    function makefriends_action()
    {
    	if($this->uid&&$this->username)
    	{
    		$id=(int)$_POST['id'];
    		if($id==$this->uid)
    		{
    			echo 4;die;
    		}
			if($_POST['type']=="1")
			{
				$itcert = $this->obj->DB_select_once("resume","`uid`='".$id."' and `idcard_status`='1'");
			}elseif($_POST['type']=="2"){
				$itcert = $this->obj->DB_select_once("company","`uid`='".$id."' and FIND_IN_SET('3',cert)");
			}elseif($_POST['type']=="3"){
				$itcert = $this->obj->DB_select_once("lt_info","`uid`='".$id."' and FIND_IN_SET('4',cert)");
			}

    		$info = $this->obj->DB_select_once("member","`uid`='".$this->uid."'");
			$isfriend = $this->obj->DB_select_once("friend","`uid`='".$this->uid."' and `nid`='".$id."'");
			if($isfriend['status']=='1')
			{
				echo 6;die;
			}elseif($isfriend['status']=='0'){
				echo 7;die;
			}else{
				if($info['usertype']==1)
				{
					$iscert = $this->obj->DB_select_once("resume","`uid`='".$this->uid."' and `idcard_status`='1'");
				}else if($info["usertype"]==2){
					$iscert = $this->obj->DB_select_once("company","`uid`='".$this->uid."' and FIND_IN_SET('3',cert)");
				}else if($info["usertype"]==3){
					$iscert = $this->obj->DB_select_once("lt_info","`uid`='".$this->uid."' and FIND_IN_SET('4',cert)");
				}
				if(!empty($iscert) && empty($itcert))
				{
					echo 2;die;
				}else if(empty($iscert)){
					echo 3;die;
				}
				$this->obj->DB_insert_once("friend","`uid`='".$this->uid."',`uidtype`='".$info['usertype']."',`nid`='".$id."',`nidtype`='".(int)$_POST['type']."',`status`='0'");
				$this->unset_remind("friend".$_POST['type'],$_POST['type']);
				echo 1;die;
			}
    	}else{
    		echo 5;die;
    	}
    }
	function Atn_action()
	{
		$id=(int)$_POST['id'];
		if((int)$_POST['id']>0){
			if($this->uid>0){
				if($_POST['id']==$this->uid){
					echo 4;die;
				}
				$atninfo = $this->obj->DB_select_once("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
				$user=$this->obj->DB_select_once("member","`uid`='".$id."'","`usertype`");
				if($user['usertype']=="1")
				{
					$table="resume";
				}elseif($user['usertype']=="2"){
					$table="company";
				}
				$comurl = $this->furl(array("url"=>"c:profile,id:".$id));
				$row=$this->obj->DB_select_once("friend_info","`uid`='".$id."'");
				$name = $row['nickname'];
				if(is_array($atninfo)&&!empty($atninfo))
				{
					$this->obj->DB_delete_all("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
					$this->obj->DB_update_all($table,"`ant_num`=`ant_num`-1","`uid`='".$id."'");
					$content="取消了对<a href=\"".$comurl."\">".$name."</a>的关注！";
					$this->addstate($content,2);
					$msg_content = "用户 ".$this->username." 取消了对你的关注！";
					$this->automsg($msg_content,(int)$_POST['id']);
					echo "2";die;
				}else{
					$this->obj->DB_insert_once("atn","`uid`='".$this->uid."',`sc_uid`='".$id."',`usertype`='".(int)$_COOKIE['usertype']."',`sc_usertype`='".$user['usertype']."',`time`='".time()."'");
					$this->obj->DB_update_all($table,"`ant_num`=`ant_num`+1","`uid`='".$id."'");
					$content="关注了<a href=\"".$comurl."\">".$name."</a>";
					$this->addstate($content,2);
					$msg_content = "用户 ".$this->username." 关注了你！";
					$this->automsg($msg_content,(int)$_POST['id']);
					echo "1";die;
				}
			}else{
				echo "3";die;
			}
		}
	}
	function atn_company_action()
	{
		$id=(int)$_POST['id'];
		if($id>0)
		{
			if($this->uid)
			{
				if($_COOKIE['usertype']!='1')
				{
					echo '4';die;
				}
				$atninfo = $this->obj->DB_select_once("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
				$comurl = $this->config['sy_weburl']."/company/index.php?id=".$id;
				$company=$this->obj->DB_select_once("company","`uid`='".$id."'","`name`");
				$name = $company['name'];
				if(is_array($atninfo)&&$atninfo)
				{
					$this->obj->DB_delete_all("atn","`uid`='".$this->uid."' AND `sc_uid`='".$id."'");
					$this->obj->DB_update_all('company',"`ant_num`=`ant_num`-1","`uid`='".$id."'");
					$content="取消了对<a href=\"".$comurl."\">".$name."</a>关注";
					$this->addstate($content,2);
					$msg_content = "用户 ".$this->username." 取消了对你的关注！";
					$this->automsg($msg_content,$id);
					echo "2";die;
				}else{
					$this->obj->DB_insert_once("atn","`uid`='".$this->uid."',`sc_uid`='".$id."',`usertype`='".(int)$_COOKIE['usertype']."',`sc_usertype`='2',`time`='".time()."'");
					$this->obj->DB_update_all('company',"`ant_num`=`ant_num`+1","`uid`='".$id."'");
					$content="关注了<a href=\"".$comurl."\">".$name."</a>";
					$this->addstate($content,2);
					$msg_content = "用户 ".$this->username." 关注了你！";
					$this->automsg($msg_content,$id);
					echo "1";die;
				}
			}else{
				echo "3";die;
			}
		}
	}
	function friendreply_action()
	{
		if($_COOKIE['uid']>0)
		{
			if($_POST['nid'])
			{
				$_POST['reply'] = iconv("utf-8", "gbk",$_POST['reply']);
				$data['nid']=(int)$_POST['nid'];
				$data['reply']=$_POST['reply'];
				$data['fid']=(int)$_POST['fid'];
				$data['uid']=$this->uid;
				$data['ctime']=time();
				$this->obj->insert_into("friend_reply",$data);
				if($_POST['fid']!=$this->uid){
					$msg_content = "您的动态有了新的回复！";
					$this->automsg($msg_content,(int)$_POST['fid']);
				}
				$comment = $this->obj->DB_select_alls("friend_reply","friend_info","a.nid='".(int)$_POST['nid']."' and a.uid=b.uid order by a.id desc limit 1");
				if(is_array($comment))
				{
					foreach($comment as $k=>$v)
					{
						$comment[$k]['ctime']=date("Y-m-d H:i",$v['ctime']);
						if($v['pic']=="")
						{
							$comment[$k]['pic'] = $this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
						}else{
							$comment[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$v['pic']);
						}
						$comment[$k]['url'] = $this->furl(array("url"=>"c:profile,id:".$v["uid"]));
						$comment[$k]['nickname'] = iconv("gbk", "utf-8",$v['nickname']);
						$comment[$k]['reply'] = iconv("gbk", "utf-8",$v['reply']);
					}
				}
				echo urldecode(json_encode($comment));die;
			}
		}else{
			echo 1;die;
		}
	}
	function mystate_action()
	{
		include_once(CONFIG_PATH."db.data.php");
		if($this->uid>0)
		{
			if($_POST['content'])
			{
				$content = str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
				$content = iconv("utf-8", "gbk",$content);
				foreach($arr_data['imface'] as $k=>$v)
				{
					if(strstr($content,"[".$k."]"))
					{
						$content=str_replace("[".$k."]","<img src=\"".$this->config['sy_weburl'].$arr_data['faceurl'].$v."\">",$content);
					}
				}
				$data['content']=$content;
				$data['uid']=$this->uid;
				$data['ctime']=time();
				$cid = $this->obj->insert_into("friend_state",$data);
				$info = $this->obj->DB_select_once("friend_info","uid='".$this->uid."'");
				if($info['pic']=="")
				{
					$info['pic'] = $this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
				}
				$info['url'] = $this->furl(array("url"=>"c:profile,id:".$this->uid));
				$info['ctime']=date("Y-m-d H:i");
				$info['cid'] = $cid;
				$info['content'] = $content;
				$info['nickname'] = iconv("gbk", "utf-8",$info['nickname']);
				echo urldecode(json_encode($info));die;
			}
		}else{
			echo 1;die;
		}
	}
	function mymessage_action()
	{
		if($this->uid>0)
		{
			if($_POST['content'])
			{
				$data['content']=iconv("utf-8", "gbk",$_POST['content']);
				$data['fid']=(int)$_POST['touid'];
				$data['nid']=(int)$_POST['nid'];
				$data['uid']=$this->uid;
				$data['ctime']=time();
				$mid = $this->obj->insert_into("friend_message",$data);
				$info = $this->obj->DB_select_once("friend_info","uid='".$this->uid."'");
				if($info['pic']==""){
					$info['pic'] = $this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
				}else{
					$info['pic'] = str_replace("..",$this->config['sy_weburl'],$info['pic']);
				}
				$info['url'] = $this->furl(array("url"=>"c:profile,id:".$this->uid));
				$info['ctime'] = date("Y-m-d H:i");
				$info['content'] = $_POST['content'];
				$info['mid'] = $mid;
				$info['nickname'] = iconv("gbk", "utf-8",$info['nickname']);
				echo urldecode(json_encode($info));;die;
			}
		}else{
			echo 1;die;
		}
	}
	function morereply_action()
	{
		$style = $this->config['sy_weburl']."/template/".$this->config['style'];
		if($_POST['nid'])
		{
			if($_POST['more']=="1")
			{
				$limit = "order by a.id desc limit 1";
			}else{
				$limit ="order by a.id asc";
			}
			$comment = $this->obj->DB_select_alls("friend_reply","friend_info","a.nid='".(int)$_POST['nid']."' and a.uid=b.uid ".$limit);
			if(is_array($comment))
			{
				foreach($comment as $k=>$v)
				{
					$comment[$k]['ctime']=date("Y-m-d H:i",$v['ctime']);
					if($v['pic']=="")
					{
						$comment[$k]['pic'] = $this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
					}
					$comment[$k]['url'] = $this->furl(array("url"=>"c:profile,id:".$v['uid']));
					$comment[$k]['nickname'] = iconv("gbk", "utf-8", $v['nickname']);
					$comment[$k]['reply'] = iconv("gbk", "utf-8", $v['reply']);
				}
				echo urldecode(json_encode($comment));die;
			}
		}
	}
	function statelist_action()
	{
		$f_info = $this->obj->DB_select_all("friend","`uid`='".$this->uid."' and `status`='1'");
		if(!empty($f_info))
		{
			foreach($f_info as $k=>$v)
			{
				$f_uid[] = $v['nid'];
			}
			$f_uids = @implode(",",$f_uid);
		}else{
			$f_uids = "0";
		}
		if($_POST['page'])
		{
			if($_POST['id'])
			{
				$row = $this->obj->DB_select_once("friend_info","`uid`='".(int)$_POST['id']."'");
				$where = "`uid`='".(int)$_POST['id']."'";
			}else{
				$row = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
				$where = "`uid`='".$this->uid."' or `uid` in (".$f_uids.")";
			}
			$limit=11;
			$page=$_POST['page']<1?1:$_POST['page'];
			$ststrsql=($page-1)*$limit;
			$list = $this->obj->DB_select_all("friend_state",$where."  ORDER BY `ctime` DESC LIMIT $ststrsql,$limit");
			$info = $this->obj->DB_select_all("friend_info");
	        $comment = $this->obj->DB_select_alls("friend_reply","friend_info","a.uid=b.uid order by a.id asc");
			if(is_array($list))
			{
				if(is_array($info))
				{
					foreach($list as $k=>$v)
					{
						$list[$k]['ctime'] = date("Y-m-d H:i",$v['ctime']);
						$list[$k]['content'] = iconv("gbk", "utf-8", $list[$k]['content']);
						foreach($info as $key=>$val)
						{
							if($v['uid']==$val['uid'])
							{
								$list[$k]['nickurl'] = $this->furl(array("url"=>"c:profile,id:$v[uid]"));
								$list[$k]['nickname'] = iconv("gbk", "utf-8", $val['nickname']);
								if($val['pic']=="")
								{
									$list[$k]['pic'] = $this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
								}else{
									$list[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$val['pic']);
								}
							}
						}
						$list[$k]['commentnum'] = "-1";
						foreach($comment as $kk=>$vv)
						{
							$vv['ctime'] = date("Y-m-d H:i",$vv['ctime']);
							if($v['id']==$vv['nid']&&$vv['nid']!="")
							{
								$list[$k]['reply'] = iconv("gbk", "utf-8", $vv['reply']);
								if($vv['pic']=="")
								{
									$list[$k]['replypic'] = $this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
								}else{
									$list[$k]['replypic'] = str_replace("..",$this->config['sy_weburl'],$vv['pic']);
								}
								$list[$k]['replyname'] = iconv("gbk", "utf-8", $vv['nickname']);
								$list[$k]['replyctime'] = $vv['ctime'];
								$list[$k]['url'] = $this->furl(array("url"=>"c:profile,id:".$vv['uid']));
								$list[$k]['commentnum'] = $list[$k]['commentnum']+1;
							}
						}
					}
				}
			}
			echo urldecode(json_encode($list));die;
		}
	}
	function face_img_action()
	{
		include_once(CONFIG_PATH."db.data.php");
		$img=$_POST['title'];
		foreach($arr_data['imface'] as $k=>$v)
		{
			if(strstr($img,"[".$k."]")){
				$img=str_replace("[".$k."]","<img src=\"".$this->config['sy_weburl'].$arr_data['faceurl'].$v."\">",$img);
			}
		}
		echo $img;die;
	}
	function show_jobsearch_action()
	{
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$Array = $this->CacheInclude($CacheArr);
		$job_index = $Array['job_index'];
		$job_type = $Array['job_type'];
		$job_name = $Array['job_name'];

		if($_POST['job_class'])
		{
			$jobids = explode(',',$_POST['job_class']);
		}
		$html='<div class="student_tips" style="left: 120px;"></div><div class="sPopupTitle290"><h1 class="student_tips_h1"><span>已选择</span></h1><div class="dialog-content"><ul class="selected clearfix">{selall}</ul></div><div class="tips_ture"><a href="javascript:determine();"  class="tips_sub1">确认</a><a href="javascript:closelayer();" class="tips_sub2">取消</a></div></div><div class="clear"></div><div class="sPopupBlock"><table id="jobTab" width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>';

		if(is_array($job_index))
		{
			foreach($job_index as $j=>$v)
			{
				if(($j+1)%2=="0")
				{
					$html.='<tr class="zebraCol0">';
				}else{
					$html.=' <tr class="zebraCol1">';
				}
				$html.='<td class="leftClass jobtypeLCla" valign="middle" nowrap="nowrap">'.$job_name[$v].'</td><td class="jobtypeItems"><table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr>';
				if(is_array($job_type[$v]))
				{
					 foreach($job_type[$v] as $jj=>$vv)
					 {
						$disabled = "";
						$allchecked = "";

						if(is_array($jobids) && in_array($vv,$jobids))
						{
							$selhtml .='<li class="all'.$vv.'"><a class="clean g3 selall" href="javascript:void(0);" data-val="'.$vv.'+'.$job_name[$vv].'"><span class="text">'.$job_name[$vv].'</span><span class="delete" data-id="'.$vv.'" ">移除</span></a></li>';
							$disabled = "disabled";
							$allchecked = "checked";
						}
						if(is_array($job_type[$vv]))
						{
							$html.=' <td class="blurItem " width="25%" id="td'.$vv.'"><span class="availItem" title="'.$job_name[$vv].'" id="span'.$vv.'" style="display:block" onclick="showjob(\''.$vv.'\');">'.$job_name[$vv].'</span>  ';

							$html.='<div style="display:none;" class="seach_tck_box_hy" id="div'.$vv.'" onmouseout="guanbiselect(\''.$vv.'\');"><label style="color:#ff9900" ><input class="availCbox" type="checkbox" onclick="check_all(\''.$vv.'\');" id="all'.$vv.'" '.$allchecked.' data-name="'.$job_name[$vv].'" value="'.$vv.'" style="margin:0 2px 0 0;">'.mb_substr($job_name[$vv],0,11,'gbk').'</label>';
							if (($jj+1)%3=="0"){
								$html.=' <div id="son_div_'.$vv.'" class="sPopupDivSubJobname sPopupDivLeftTop" style="width: 360px; background-position: -81px -1px; z-index: 1001; left:-165px; top: 17px; ;">';
							}else{
								$html.='<div id="son_div_'.$vv.'" class="sPopupDivSubJobname sPopupDivLeftTop" style="width: 360px; background-position: 197px -1px; z-index: 1001; left:-3px; top: 17px; ;">';
							}
							$html.='<div class="paddingBlock"><table class="chebox" width="100" cellspacing="0" cellpadding="0" border="0"><tbody><tr>';
							foreach($job_type[$vv] as $jjj=>$vvv)
							{
								$checked = "";
								if(is_array($jobids) && in_array($vvv,$jobids))
								{
									$selhtml .='<li class="job_class_'.$vvv.' parent_'.$vv.'"><a class="clean g3 selall" href="javascript:void(0);" data-val="'.$vvv.'+'.$job_name[$vvv].'"><span class="text">'.$job_name[$vvv].'</span><span class="delete" data-id="'.$vvv.'" data-pid ="'.$vv.'">移除</span></a></li>';
									if($allchecked=='')
									{
										$checked = "checked";
									}

								}

								$html.='<td class="mOutItem" width="50%" id="label'.$vv.'" ><label class="noselItem" onclick="check_this(\''.$vvv.'\');"><input id="job_class_'.$vvv.'" class="seach_box_cheack label'.$vv.'" '.$disabled.' '.$allchecked.' '.$checked.' onclick="check_this(\''.$vvv.'\');" type="checkbox" data-pid = "'.$vv.'" data-name="'.$job_name[$vvv].'" value="'.$vvv.'+'.$job_name[$vvv].'" >'.$job_name[$vvv].' </label></td>';
								if(($jjj+1)%2=="0")
								{
									$html.=' </tr> <tr>';
								}
							}
							$html.=' </tr></tbody> </table></div></div></div></td>';
						}else{
							$html.="<td  class=\"mOutItem\" width=\"25%\"><label class=\"noselItem\" onclick=\"check_this('".$vv."');\"><input id=\"job_class_".$vv."\" class=\"seach_box_cheack label50\" type=\"checkbox\" value=\"".$vv."+".$job_name[$vv]."\" data-name=\"".$job_name[$vv]."\" data-pid=\"50\" onclick=\"check_this('".$vv."');\">".$job_name[$vv]."</label></td>";
						}
						if( ($jj+1)%4==0)
						{
							$html.=' </tr><tr> ';
						}
					}
					if(count($job_type[$v])<3){
						$html.=' <td width="25%"></td><td width="25%"></td>  ';
					}elseif(count($job_type[$v])<4){
						$html.=' <td width="25%"></td>';
					}
				}
				$html.='</tr></tbody></table></td></tr>';
			}
		}
		$html.=' </tbody></table></div>';
		$html = str_replace('{selall}',$selhtml,$html);
		echo $html;
		die;
	}
	function show_citysearch_action()
	{
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$Array = $this->CacheInclude($CacheArr);
		$city_index = $Array['city_index'];
		$city_type = $Array['city_type'];
		$city_name = $Array['city_name'];
		$html=' <div class="student_tips" style="left: 550px;"></div><div class="clear"></div><div class="sPopupBlock sPopupBlock_city"><div class="clear" style="height: 5px;"></div><div class="pCityItemB" style="display: block;"><div class="sPopupTabCB"><table class="sPopupTabC" cellspacing="0" cellpadding="0" border="0"><tbody><tr>';
		if(is_array($city_index))
		{
			foreach($city_index as $j=>$v)
			{
				$html.=' <td class="blurItem" width="15%" id="td_city'.$v.'" onclick="showcity(\''.$v.'\')" onmouseout="guanbicity(\''.$v.'\');"><span class="availItem" style="display: ;" id="span_city'.$v.'" >'.$city_name[$v].'</span>
                <div class="seach_tck_box_hy" style="display:none;" id="div_city'.$v.'" >';
                $html.='<div class="s_b_city_icon"></div><span onclick="checkcity(\''.$v.'\',\''.$city_name[$v].'\')">'.$city_name[$v].'</span>';
				if (($j+1)%6==0 || ($j+1)%6==4 || ($j+1)%6==5){
					$html.='<div class="sPopupDivSubJobname sPopupDivSubCity" style="background-position:-2px -1px;z-index:1001;left:-246px; top: 19px; ">';
				}else{
					$html.=' <div class="sPopupDivSubJobname sPopupDivSubCity" style="background-position: 105px -1px; z-index: 1001; left: -5px; top:19px; ">';
				}
				$html.=' <div class="paddingBlock">';
				if(is_array($city_type[$v]))
				{
					foreach($city_type[$v] as $jj=>$vv)
					{
						$html.='<span class="subCboxItem mOutItem" style="width:107px;" onclick="checkcity(\''.$vv.'\',\''.$city_name[$vv].'\');"> <span class="availItem" >'.$city_name[$vv].'</span> </span>';
					}
				}
				$html.=' <div class="clear"></div></div></div></td>';
				if(($j+1)%6==0)
				{
					$html.='</tr><tr>';
				}
			}
		}
		echo $html.=' <tr></tbody></table><div class="clear" style="height: 5px;"></div></div></div></div>';die;
	}
	function show_leftjob_action()
	{
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$Array = $this->CacheInclude($CacheArr);
		$job_index = $Array['job_index'];
		$job_type = $Array['job_type'];
		$job_name = $Array['job_name'];
		$html.='<ul>';
		if(is_array($job_index))
		{
			foreach($job_index as $j=>$v)
			{
				if($j<17)
				{
					$html.='<li class="lst'.$j.' " onmouseover="show_job(\''.$j.'\',\'0\');" onmouseout="hide_job(\''.$j.'\');"> <b></b> <a class="link" href="index.php?m=com&c=search&job1='.$v.'" title="'.$job_name[$v].'">'.$job_name[$v].'</a> <i></i><div class="lstCon"><div class="lstConClass">';
					if(is_array($job_type[$v]))
					{
						foreach($job_type[$v] as $vv)
						{
							$html.=' <dl><dt> <a  href="index.php?m=com&c=search&job1='.$v.'&job1_son='.$vv.'" title="'.$job_name[$vv].'">'.$job_name[$vv].'</a> </dt><dd> ';
							if(is_array($job_type[$vv]))
							{
								foreach($job_type[$vv] as $vvv)
								{
									$html.=' <a  href="index.php?m=com&c=search&job1='.$v.'&job1_son='.$vv.'&job_post='.$vvv.'" title="'.$job_name[$vvv].'">'.$job_name[$vvv].' </a>';
								}
							}
							$html.=' </dd><dd style="display:block;clear:both;float:inherit;width:100%;font-size:0;line-height:0;"></dd></dl>';
						}
					}
					$html.=' </div> </div></li>';
				}
			}
		}
		echo $html.=' </ul>';die;
	}
	function send_email_action()
	{
		$this->send_msg_email(array("email"=>$_POST['email'],"type"=>"remind"));
		echo 1;die;
	}
	function send_moblie_action()
	{
		$this->send_msg_email(array("moblie"=>$_POST['moblie'],"type"=>"remind"));
		echo 1;die;
	}
	function Refresh_job_action()
	{
		$num=count($_POST['ids']);
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
		if($statis['vip_etime']<time())
		{
			if($statis['breakjob_num']>=$num)
			{
				$value="`breakjob_num`=`breakjob_num`-$num";
			}else{
				if($this->config['com_integral_online']=="1")
				{
					$integral=$this->config['integral_jobefresh']*($num-$statis['breakjob_num']);
					if($statis['integral']<$integral)
					{
						echo "会员刷新职位数、".$this->config['integral_pricename']."均不足！";die;
					}else{
						$value="`breakjob_num`='0',`integral`=`integral`-$integral";
						$this->insert_company_pay($integral,2,$this->uid,'批量刷新职位',1,8);
					}
				}else{
					echo "会员刷新职位数不足！";die;
				}
			}
			$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
		}
		$ids=$this->pylode(",",$_POST['ids']);
		$nid=$this->obj->DB_update_all("company_job","`lastupdate`='".time()."'","`id` in (".$ids.") and `uid`='".$this->uid."'");
		$this->obj->DB_update_all("company","`jobtime`='".$_POST['lastupdate']."'","`uid`='".$this->uid."'");
		if($nid)
		{
			echo 1;die;
		}else{
			echo "刷新失败！";die;
		}
	}
	function ajax_city_action()
	{
		if($_POST['ptype']=='city')
		{
			include(PLUS_PATH."city.cache.php");
			if(is_array($city_type[$_POST['id']]))
			{
				$data.="<div style=\"width:100%; overflow:auto; overflow-x:hidden\"><ul>";
				foreach($city_type[$_POST['id']] as $v)
				{
					if($_POST['gettype']=="citys")
					{
						$data.='<li><a href="javascript:;" onclick="select_city(\''.$v.'\',\'citys\',\''.$city_name[$v].'\',\'three_city\',\'city\');">'.$city_name[$v].'</a></li>';
					}else{
						$data.='<li><a href="javascript:;" onclick="selects(\''.$v.'\',\'three_city\',\''.$city_name[$v].'\');">'.$city_name[$v].'</a></li>';
					}
				}
				$data.="</ul></div>";
			}
		}else{
			include(PLUS_PATH."job.cache.php");
			if(is_array($job_type[$_POST['id']]))
			{
				$data.="<div style=\"width:100%; overflow:auto; overflow-x:hidden\"><ul>";
				foreach($job_type[$_POST['id']] as $v)
				{
					if($_POST['gettype']=="job1_son")
					{
						$data.='<li><a href="javascript:;" onclick="select_city(\''.$v.'\',\'job1_son\',\''.$job_name[$v].'\',\'job_post\',\'job\');">'.$job_name[$v].'</a></li>';
					}else{
						$data.='<li><a href="javascript:;" onclick="selects(\''.$v.'\',\'job_post\',\''.$job_name[$v].'\');">'.$job_name[$v].'</a></li>';
					}
				}
				$data.="</ul></div>";
			}
		}

		echo $data;
	}
	function wap_job_action()
	{
		include(PLUS_PATH."job.cache.php");
		if($_POST['type']==1)
		{
			$data="<option value=''>--请选择--</option>";
		}
		if(is_array($job_type[$_POST['id']])){
			foreach($job_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$job_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function wap_city_action()
	{
		include(PLUS_PATH."city.cache.php");
		if($_POST['type']==1)
		{
			$data="<option value=''>--请选择--</option>";
		}
		if(is_array($city_type[$_POST['id']])){
			foreach($city_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$city_name[$v]."</option>";
			}
		}
		echo $data;
	}
}
?>