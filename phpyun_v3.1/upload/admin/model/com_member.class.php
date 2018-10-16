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
class com_member_controller extends common
{
	function index_action()
	{
		$where="`usertype`='2' ";
		if($_GET['comsearch']&&trim($_GET['keyword'])!=""){
			 if($_GET['type']=="1"){
				$where .=" and `username` LIKE'%".$_GET['keyword']."%'";
			 }elseif($_GET['type']=="2"){
				$where .=" and `email` LIKE '%".$_GET['keyword']."%'";
			 }else{
				$where .=" and `moblie` LIKE '%".$_GET['keyword']."%'";
			 }
			 $urlarr['type']=$_GET['type'];
			 $urlarr['keyword']=$_GET['keyword'];
			$urlarr['comsearch']=$_GET['comsearch'];
		}
		if($_GET['status']){
			if($_GET['status']=="1"){
				$where .= " and `status`='1'";
			}elseif($_GET['status']=="3"){
				$where .= " and `status`='3'";
			}elseif($_GET['status']=="2"){
				$where .= " and `status`='2'";
			}else{
				$where .= " and `status`='0'";
			}
			$urlarr['status']=$_GET['status'];
		}

		if($_SESSION['admin_city']){
			$where.=" and `cityid`='".$_SESSION['admin_city']."'";
		}
		if($_SESSION['admin_hy']){
			$where.=" and `hy`='".$_SESSION['admin_hy']."'";
		}
		if($_SESSION['def_city'] || $_SESSION['def_hy']){
			if($_SESSION['def_city']){
				$session[]="`cityid` in (".$_SESSION['def_city'].")";
			}
			if($_SESSION['def_hy']){
				$session[]="`hy` in (".$_SESSION['def_hy'].")";
			}
			$where.=" and (".@implode(" or ",$session).")";
		}
		if($_GET['order']){
			$where1.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by `uid` desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index","com_member",$urlarr);
		$userrows=$this->get_page("member",$where,$pageurl);
		if($userrows&&is_array($userrows)){
			foreach($userrows as $val){
				$uids[]=$val['uid'];
			}
			$rating=$this->obj->DB_select_all("company_statis","`uid` in(".@implode(',',$uids).")","`uid`,`rating_name`,`rating`");
			$company=$this->obj->DB_select_all("company","`uid` in(".@implode(',',$uids).")","`cert`,`name`,`uid`");
			foreach($userrows as $key=>$val){
				foreach($rating as $value){
					if($val['uid']==$value['uid']){
						$userrows[$key]['rat_name']=$value['rating_name'];
						$userrows[$key]['rating']=$value['rating'];
					}
				}
				foreach($company as $v){
					if($val['uid']==$v['uid']){
						$userrows[$key]['cert']=$v['cert'];
						$userrows[$key]['name']=$v['name'];
					}
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("userrows",$userrows);
		$this->yuntpl(array('admin/admin_member_comlist'));
	}

	function lockinfo_action(){
		$userinfo = $this->obj->DB_select_once("member","`uid`=".$_POST['uid'],"`lock_info`");
		echo $userinfo['lock_info'];die;
	}

	function status_action(){
		 extract($_POST);
		 $id = @explode(",",$uid);
		 $member=$this->obj->DB_select_all("member","`uid` in (".$uid.")","`email`,`uid`");
		 $smtp = $this->email_set();
		 if(is_array($member)){
			foreach($member as $v){
				$idlist[] =$v['uid'];
				$this->send_msg_email(array("email"=>$v['email'],"status_info"=>$statusbody,"date"=>date("Y-m-d H:i:s"),"type"=>"userstatus"));
			}
			if(trim($statusbody)){
				$lock_info=$statusbody;
			}
			$aid = @implode(",",$idlist);
			$this->obj->DB_update_all("company_job","`r_status`='".$status."'","`uid` IN (".$aid.")");
			$this->obj->DB_update_all("company","`r_status`='".$status."'","`uid` IN (".$aid.")");
			$id=$this->obj->DB_update_all("member","`status`='".$status."',`lock_info`='".$lock_info."'","`uid` IN (".$aid.")");
			$id?$this->obj->ACT_layer_msg("审核(ID:".$aid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("审核(ID:".$aid.")设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg( "非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function edit_action()
	{
		if((int)$_GET['id'])
		{
			$com_info = $this->obj->DB_select_once("member","`uid`='".$_GET['id']."'");
			$row = $this->obj->DB_select_once("company","`uid`='".$_GET['id']."'");
			$stati = $this->obj->DB_select_once("company_statis","`uid`='".$_GET['id']."'");
			$rating_list = $this->obj->DB_select_all("company_rating","`category`=1");
			$this->yunset("statis",$stati);
			$this->yunset("row",$row);
			$this->yunset("rating_list",$rating_list);
			$this->yunset("rating",$_GET['rating']);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
			$this->yunset("com_info",$com_info);

			$CacheArr['com'] =array('comdata','comclass_name');
			$CacheArr['city'] =array('city_index','city_type','city_name');
			$CacheArr['industry'] =array('industry_index','industry_name');
			$this->CacheInclude($CacheArr);

		}
		if($_POST['com_update'])
		{
			$email=$_POST['email'];
			$uid=$_POST['uid'];
			$user = $this->obj->DB_select_once("member","`email`='$email' and `uid`<>'$uid'");
			if(is_array($user)){
				$msg = "邮箱已存在！";
				$this->obj->ACT_layer_msg( $msg,8,$_SERVER['HTTP_REFERER'],2,1);
			}else{
				$this->obj->DB_update_all("company","`r_status`='".$_POST['status']."'","`uid`=".$_POST['uid']." ");
				if($_POST['status']=='2'){ 
					$mem = $this->obj->DB_select_once("member","`uid`=".$_POST['uid'],"`email`,`status`");
					$smtp = $this->email_set(); 
					if($mem['status']!='2'){  
						$this->send_msg_email(array("email"=>$mem['email'],"lock_info"=>$_POST['lock_info'],"type"=>"lock"));
						$this->obj->DB_update_all("member","`lock_info`='".$_POST['lock_info']."'","`uid`='".$_POST['uid']."'");
					}
				}
				unset($_POST['com_update']);
				$rating_name = $_POST['rating_name'];
				unset($_POST['rating_name']);
				$post['uid']=$_POST['uid'];
				$post['password']=$_POST['password'];
				$post['email']=$_POST['email'];
				$post['moblie']=$_POST['moblie'];
				$post['status']=$_POST['status'];
				$post['address']=$_POST['address'];
				$nid = $this->uc_edit_pw($post,1,"index.php?m=com_member");
				$value.="`name`='".$_POST['name']."',";
				$value.="`hy`='".$_POST['hy']."',";
				$value.="`pr`='".$_POST['pr']."',";
				$value.="`provinceid`='".$_POST['provinceid']."',";
				$value.="`cityid`='".$_POST['cityid']."',";
				$value.="`mun`='".$_POST['mun']."',";
				$value.="`linkphone`='".$_POST['linkphone']."',";
				$value.="`linktel`='".$_POST['moblie']."',";
				$value.="`money`='".$_POST['money']."',";
				$value.="`zip`='".$_POST['zip']."',";
				$value.="`linkman`='".$_POST['linkman']."',";
				$value.="`linkjob`='".$_POST['linkjob']."',";
				$value.="`linkqq`='".$_POST['linkqq']."',";
				$value.="`website`='".$_POST['website']."',";
				$content=str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',''),html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
				$value.="`content`='".$content."',";
				$value.="`admin_remark`='".$_POST['admin_remark']."',";
				$value.="`linkmail`='".$_POST['email']."'";
				$this->obj->DB_update_all("company",$value,"`uid`='".$_POST['uid']."'");
				$rat_arr = @explode("+",$rating_name);
				$statis = $this->obj->DB_select_once("company_statis","`uid`='".$_POST['uid']."'");
				if($rat_arr[0] != $statis['rating'])
				{
					$rat_value=$this->rating_info($rat_arr[0]);
					$this->obj->DB_update_all("company_statis",$rat_value,"`uid`='".$_POST['uid']."'");
				}else{
					$value3.="`job_num`='".$_POST['job_num']."',";
					$value3.="`down_resume`='".$_POST['down_resume']."',";
					$value3.="`editjob_num`='".$_POST['editjob_num']."',";
					$value3.="`invite_resume`='".$_POST['invite_resume']."',";
					$value3.="`breakjob_num`='".$_POST['breakjob_num']."'";
					$this->obj->DB_update_all("company_statis",$value3,"`uid`='".$_POST['uid']."'");
				}
				$value2.="`com_name`='".$_POST['name']."',";
				$value2.="`pr`='".$_POST['pr']."',";
				$value2.="`mun`='".$_POST['mun']."',";
				$value2.="`com_provinceid`='".$_POST['provinceid']."',";
				$value2.="`rating`='".$rat_arr[0]."',";
				$value2.="`r_status`='".$_POST['status']."'";
				$this->obj->DB_update_all("company_job",$value2,"`uid`='".$_POST['uid']."' ");
				$lasturl=str_replace("&amp;","&",$_POST['lasturl']);
				$this->obj->ACT_layer_msg( "企业会员(ID:".$_POST['uid'].")修改成功！",9,$lasturl,2,1);
			}
		}
		$this->yuntpl(array('admin/admin_member_comedit'));
	}
	function rating_action(){
		$rating_name = $_POST['rat'];
		$rat_arr = @explode("+",$rating_name);
		$statis = $this->obj->DB_select_all("company_statis","`uid`='".$_POST['uid']."'");
		if(is_array($statis))
		{
			$value=$this->rating_info($rat_arr[0]);
			$this->obj->DB_update_all("company_statis",$value,"`uid`='".$_POST['uid']."'");
		}else{
			$value="`uid`='".$_POST['uid']."',";
			$value.=$this->rating_info($rat_arr[0]);
			$this->obj->DB_insert_once("company_statis",$value);
		}
		echo "1";die;
	}
	function add_action()
	{
		$rating_list = $this->obj->DB_select_all("company_rating","`category`=1");
		if($_POST['submit'])
		{
			extract($_POST);
			if($username==""||strlen($username)<2||strlen($username)>15)
			{
				$msg = "会员名不能为空或不符合要求！";
			}elseif($password==""||strlen($username)<2||strlen($username)>15){
				$msg = "密码不能为空或不符合要求！";
			}elseif($email==""){
				$msg = "email不能为空！";
			}else{
				if($this->config['sy_uc_type']=="uc_center"){
					$this->obj->uc_open();
					$user = uc_get_user($username);
				}else{
					$user = $this->obj->DB_select_once("member","`username`='$username' OR `email`='$email'");
				}
				if(is_array($user))
				{
					$msg = "用户名或邮箱已存在！";
				}else{
					$ip = $this->obj->fun_ip_get();
					$time = time();
					if($this->config['sy_uc_type']=="uc_center")
					{
						$uid=uc_user_register($_POST['username'],$_POST['password'],$_POST['email']);
						if($uid<0)
						{
							$this->obj->get_admin_msg("index.php?m=com_member&c=add","该邮箱已存在！");
						}else{
							list($uid,$username,$email,$password,$salt)=uc_get_user($username);
							$value = "`username`='$username',`password`='$password',`email`='$email',`usertype`='2',`address`='$address',`status`='$status',`salt`='$salt',`moblie`='$moblie',`reg_date`='$time',`reg_ip`='$ip'";
						}
					}else{
						$salt = substr(uniqid(rand()), -6);
						$pass = md5(md5($password).$salt);
						$value = "`username`='$username',`password`='$pass',`email`='$email',`usertype`='2',`address`='$address',`status`='$status',`salt`='$salt',`moblie`='$moblie',`reg_date`='$time',`reg_ip`='$ip'";
					}
					$nid = $this->obj->DB_insert_once("member",$value);
					$new_info = $this->obj->DB_select_once("member","`username`='$username'");
					$uid = $new_info['uid'];
					if($uid>0)
					{
						$this->obj->DB_insert_once("company","`uid`='$uid',`name`='$name',`linktel`='$moblie',`linkmail`='$email',`address`='$address'");
						$rat_arr = @explode("+",$rating_name);
						$value = "`uid`='$uid',";
						$value.=$this->rating_info($rat_arr[0]);
						$this->obj->DB_insert_once("company_statis",$value);
						$this->obj->DB_insert_once("friend_info","`uid`='$uid',`nickname`='$name',`usertype`='2'");
						$msg="会员(ID:".$uid.")添加成功";
					}
				}
			}
			if($_POST['type']){
				echo "<script type='text/javascript'>window.location.href='index.php?m=admin_company_job&c=show&uid=".$nid."'</script>";die;
			}else{
				$this->obj->ACT_layer_msg($msg,9,"index.php?m=com_member&c=add",2,1);
			}
		}
		$this->yunset("get_info",$_GET);
		$this->yunset("rating_list",$rating_list);
		$this->yuntpl(array('admin/admin_member_comadd'));
	}
	function rating_info($id)
	{
		$row = $this->obj->DB_select_once("company_rating","`id`='$id'");
		$value="`rating`='$id',";
		$value.="`integral`='".$this->config['integral_reg']."',";
		$value.="`rating_name`='".$row['name']."',";
		$value.="`job_num`='".$row['job_num']."',";
		$value.="`down_resume`='".$row['resume']."',";
		$value.="`invite_resume`='".$row['interview']."',";
		$value.="`editjob_num`='".$row['editjob_num']."',";
		$value.="`breakjob_num`='".$row['breakjob_num']."'";
		if($row['type']=='2')
		{
			$time=time()+86400*$row['service_time'];
			$value.=",`vip_etime`='".$time."'";
		}
		return $value;
	}

	function del_action()
	{
	    if($_GET['del']){
			$this->check_token();
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
	    			$layer_type=1;
	    			$uids = @implode(",",$del);
	    			foreach($del as $k=>$v){
	    				$this->obj->delfiledir("../upload/tel/".intval($v));
	    			}
				    $company=$this->obj->DB_select_all("company","`uid` in (".$uids.") and `logo`<>''");
				    if(is_array($company)){
				    	foreach($company as $v){
				    		$this->obj->unlink_pic(".".$v['logo']);
				    		$this->obj->unlink_pic(".".$v['firmpic']);
				    	}
				    }
		    	    $cert=$this->obj->DB_select_all("company_cert","`uid` in (".$uids.") and `type`='3'");
		    	    if(is_array($cert)){
				    	foreach($cert as $v){
				    		$this->obj->unlink_pic("../".$v['check']);
				    	}
				    }
		    	    $product=$this->obj->DB_select_all("company_product","`uid` in (".$uids.")");
		    	    if(is_array($product)){
		    	    	foreach($product as $val){
		    	    		$this->obj->unlink_pic("../".$val['pic']);
		    	    	}
		    	    }
		    	    $show=$this->obj->DB_select_all("company_show","`uid` in (".$uids.")");
		    	    if(is_array($show)){
		    	    	foreach($show as $val){
		    	    		$this->obj->unlink_pic("../".$val['picurl']);
		    	    	}
		    	    }
		    	    $uhotjob=$this->obj->DB_select_all("hotjob","`uid` in (".$uids.")");
		    	    if(is_array($uhotjob)){
		    	    	foreach($uhotjob as $val){
		    	    		$this->obj->unlink_pic("../".$val['picurl']);
		    	    	}
		    	    }
		    	  	$banner=$this->obj->DB_select_all("banner","`uid` in (".$uids.")");
		    	    if(is_array($banner)){
		    	    	foreach($banner as $val)
		    	    	{
		    	    		$this->obj->unlink_pic($val['pic']);
		    	    	}
		    	    }
		    	    $friend_pic = $this->obj->DB_select_all("friend_info","`uid` in (".$uids.") and `pic`<>''");
		    		if(is_array($friend_pic))
		    		{
		    	    	foreach($friend_pic as $val)
		    	    	{
		    	    		$this->obj->unlink_pic($val['pic']);
		    	    		$this->obj->unlink_pic($val['pic_big']);
		    	    	}
		    		}
					$del_array=array("member","company","company_job","company_job_link","company_cert","company_news","company_order","company_product","company_show","banner","company_statis","friend_info","friend_state","question","attention","lt_job","hotjob");
					foreach($del_array as $value)
					{
						$this->obj->DB_delete_all($value,"`uid` in (".$uids.")","");
					}
		    	    $this->obj->DB_delete_all("company_pay","`com_id` in (".$uids.")"," ");
					$this->obj->DB_delete_all("atn","`uid` in (".$uids.") or `scid` in (".$uids.")","");
					$this->obj->DB_delete_all("look_resume","`com_id` in (".$uids.")","");
					$this->obj->DB_delete_all("fav_job","`com_id` in (".$uids.")","");
					$this->obj->DB_delete_all("userid_msg","`fid` in (".$uids.")","");
					$this->obj->DB_delete_all("userid_job","`com_id` in (".$uids.")","");
					$this->obj->DB_delete_all("message","`fa_uid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend_reply","`fid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend","`uid` in (".$uids.") or `nid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend_foot","`uid` in (".$uids.") or `fid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend_message","`uid`='".$del."' or `fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("msg","`job_uid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("blacklist","`c_uid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("rebates","`job_uid` in (".$uids.") or `uid` in (".$uids.")"," ");
		    	    $this->obj->DB_delete_all("lt_job","`uid` in (".$uids.")"," ");
		    	    $this->obj->DB_delete_all("report","`p_uid` in ($uids) or `c_uid` in ($uids)","");
		    	}else{
		    		$layer_type=0;
					$uids=$del = intval($del);
					$uids=$del;
		    		$friend_pic = $this->obj->DB_select_once("friend_info","`uid`='".$del."' and `pic`<>''");
		    		if(is_array($friend_pic))
		    		{
		    			$this->obj->unlink_pic($friend_pic['pic']);
		    			$this->obj->unlink_pic($friend_pic['pic_big']);
		    		}
		    		$this->obj->delfiledir("../upload/tel/".$del);
				    $company=$this->obj->DB_select_once("company","`uid`='".$del."' and `logo`<>''");
				    $this->obj->unlink_pic(".".$company['logo']);
				    $this->obj->unlink_pic(".".$company['firmpic']);
		    	    $cert=$this->obj->DB_select_once("company_cert","`uid`='".$del."' and `type`='3'");
		    	    $this->obj->unlink_pic("../".$cert['check']);
		    	    $product=$this->obj->DB_select_all("company_product","`uid`='".$del."'");
		    	    if(is_array($product))
		    	    {
		    	    	foreach($product as $v)
		    	    	{
		    	    		$this->obj->unlink_pic("../".$v['pic']);
		    	    	}
		    	    }
		    	    $show=$this->obj->DB_select_all("company_show","`uid`='".$del."'");
		    	    if(is_array($show))
		    	    {
		    	    	foreach($show as $v)
		    	    	{
		    	    		$this->obj->unlink_pic("../".$v['picurl']);
		    	    	}
		    	    }
			    	$uhotjob=$this->obj->DB_select_all("hotjob","`uid`='".$del."'");
		    	    if(is_array($uhotjob))
		    	    {
		    	    	foreach($uhotjob as $val)
		    	    	{
		    	    		$this->obj->unlink_pic("../".$val['picurl']);
		    	    	}
		    	    }
		    	    $banner=$this->obj->DB_select_once("banner","`uid`='".$del."'");
					$this->obj->unlink_pic($banner['pic']);
					$del_array=array("member","company","company_job","company_job_link","company_cert","company_news","company_order","company_product","company_show","banner","company_statis","friend_state","question","attention","lt_job","hotjob");
					foreach($del_array as $value)
					{
						$this->obj->DB_delete_all($value,"`uid`='".$del."'","");
					}
					$this->obj->DB_delete_all("company_pay","`com_id`='".$del."'"," ");
		    	    $this->obj->DB_delete_all("atn","`uid`='".$del."' or `scid`='".$del."'","");
		    	    $this->obj->DB_delete_all("look_resume","`com_id`='".$del."'","");
		    	    $this->obj->DB_delete_all("fav_job","`com_id`='".$del."'","");
		    	    $this->obj->DB_delete_all("userid_msg","`fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("userid_job","`com_id`='".$del."'","");
		    	    $this->obj->DB_delete_all("message","`fa_uid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend","`uid`='".$del."' or `nid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend_foot","`uid`='".$del."' or `fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend_message","`uid`='".$del."' or `fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend_reply","`fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("msg","`job_uid`='".$del."'","");
		    	    $this->obj->DB_delete_all("blacklist","`c_uid`='".$del."'","");
		    	    $this->obj->DB_delete_all("rebates","`job_uid`='".$del."' or `uid` ='".$del."'"," ");
		    	    $this->obj->DB_delete_all("lt_job","`uid`='".$del."'"," ");
		    	    $this->obj->DB_delete_all("report","`p_uid`='".$del."' or `c_uid`='".$del."'");
		    	}
				$this->layer_msg( "企业会员(ID:".$uids.")删除成功！",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,0,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	}
}
?>