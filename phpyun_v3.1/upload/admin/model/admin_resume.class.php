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
class admin_resume_controller extends common
{
	function index_action(){
		$time = time();
        $wheres = "1 ";
		if($_GET['hy']){
			$wheres .= " AND `hy` = '".$_GET['hy']."' ";
			$urlarr['hy']=$_GET['hy'];
		}
		if($_GET['job1']){
			$wheres .= " AND `job1` = '".$_GET['job1']."' ";
			$urlarr['job1']=$_GET['job1'];
		}
		if($_GET['job1_son']){
			$wheres .= " AND `job1_son` = '".$_GET['job1_son']."' ";
			$urlarr['job1_son']=$_GET['job1_son'];
		}
		if($_GET['job_post']){
			$wheres .= " AND `job_post` = '".$_GET['job_post']."' ";
			$urlarr['job_post']=$_GET['job_post'];
		}
		if($_GET['provinceid']){
			$wheres .= " AND `provinceid` = '".$_GET['provinceid']."' ";
			$urlarr['provinceid']=$_GET['provinceid'];
		}
		if($_GET['cityid']){
			$wheres .= " AND `cityid` = '".$_GET['cityid']."' ";
			$urlarr['cityid']=$_GET['cityid'];
		}
		if($_GET['three_cityid']){
			$wheres .= " AND `three_cityid` = '".$_GET['three_cityid']."' ";
			$urlarr['three_cityid']=$_GET['three_cityid'];
		}
		if($_GET['salary']){
			$wheres .= " AND `salary` = '".$_GET['salary']."' ";
			$urlarr['salary']=$_GET['salary'];
		}
		if($_GET['type']){
			$wheres .= " AND `type` = '".$_GET['type']."' ";
			$urlarr['type']=$_GET['type'];
		}
		if($_GET['number']){
			$wheres .= " AND `number` = '".$_GET['number']."' ";
			$urlarr['number']=$_GET['number'];
		}
		if($_GET['exp']){
			$wheres .= " AND `exp` = '".$_GET['exp']."' ";
			$urlarr['exp']=$_GET['exp'];
		}
		if($_GET['report']){
			$wheres .= " AND `report` = '".$_GET['report']."' ";
			$urlarr['report']=$_GET['report'];
		}
		if($_GET['sex']){
			$wheres .= " AND `sex` = '".$_GET['sex']."' ";
			$urlarr['sex']=$_GET['sex'];
		}
		if($_GET['edu']){
			$wheres .= " AND `edu` = '".$_GET['edu']."' ";
			$urlarr['edu']=$_GET['edu'];
		}
		if($_GET['marriage']){
			$wheres .= " AND `marriage` = '".$_GET['marriage']."' ";
			$urlarr['marriage']=$_GET['marriage'];
		}
        if($_GET['keyword']){
			$wheres .= " AND `name` like '%".$_GET['keyword']."%' ";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$where="1";
		if($_GET['news_search']){
			extract($_GET);
			if ($type_n){
				$where .=" and `type`=$type_n";
				$urlarr['type_n']=$type_n;
			}
			if ($salary_n){
				$where .=" and `salary`=$salary_n";
				$urlarr['salary_n']=$salary_n;
			}
			if ($report_n){
				$where .=" and `report`=$report_n";
				$urlarr['report_n']=$report_n;
			}
			if ($s_news_id!=""){
				$where.=" and `name` like '%".$s_news_id."%'";
			}
			$urlarr['s_news_id']=$_GET['s_news_id'];
			$urlarr['news_search']=$_GET['news_search'];
		}
		if($_GET['id'])
		{
			$where.=" and `id`='".$_GET['id']."'";
			$urlarr['id']=$_GET['id'];
		}
		if($_GET['order'])
		{
			if($_GET['t']=="time")
			{
				$where.=" order by `lastupdate` ".$_GET['order'];
			}else{
				$where.=" order by ".$_GET['t']." ".$_GET['order'];
			}
		}else{
			$where.=" order by `id` desc";
		}
		$urlarr['order']=$_GET['order'];
		if($_GET['searchid']){
			$where = "`id` in (".$_GET['searchid'].")";
			$urlarr['searchid']=$_GET['searchid'];
		}
		if($_GET['advanced']){
			$where = $wheres;
			$urlarr['advanced']=$_GET['advanced'];
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("resume_expect",$where,$pageurl,$this->config['sy_listnum']);
		include APP_PATH."/plus/job.cache.php";
		include APP_PATH."/plus/user.cache.php";
		include APP_PATH."/plus/city.cache.php";
		if(is_array($rows)){
			foreach($rows as $k=>$v){
			    $rows[$k]['edu_n']=$userclass_name[$v['edu']];
				$rows[$k]['exp_n']=$userclass_name[$v['exp']];
				$rows[$k]['cityid_n']=$city_name[$v['cityid']];
				$rows[$k]['salary_n']=$userclass_name[$v['salary']];
				$rows[$k]['report_n']=$userclass_name[$v['report']];
				$rows[$k]['type_n']=$userclass_name[$v['type']];
				$job_classid=@explode(",",$v['job_classid']);
				$job_class_name=array();
				if(is_array($job_classid)){
					$i=0;
					foreach($job_classid as $key=>$v){
						$jobname[$key]=$v;
						if($v!=""){
							$i=$i+1;
						}
						$job_class_name[]=$job_name[$v];
					}
					$rows[$k]['jobnum']=$i;
					$rows[$k]['job_post_n']=$job_name[$jobname[0]];
				}
				$rows[$k]['job_class_name']=@implode('、',$job_class_name);
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("rows",$rows);
		$this->user_cache();

		$this->yuntpl(array('admin/admin_resume'));
	}
	function del_action(){

		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
			    	foreach($del as $v){
			    	   $this->del_member($v);
			    	}
					$layer_type='1';
		    	}else{
		    		 $this->del_member($del);
					 $layer_type='0';
		    	}
				$this->layer_msg( "删除成功！",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result = $this->del_member($_GET['id']);
			isset($result)?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('非法操作！',3,0,$_SERVER['HTTP_REFERER']);
		}
	}

	function del_member($id)
	{
		$id_arr = @explode("-",$id);

		if($id_arr[0])
		{
			$result=$this->obj->DB_delete_all("resume_expect","`id`='".$id_arr[0]."'" );
			$del_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume");
			foreach($del_array as $v){
				$this->obj->DB_delete_all($v,"`eid`='".$id_arr[0]."'");
			}
			$this->obj->DB_update_all("member_statis","`resume_num`=`resume_num`-1","`uid`='".$id_arr[1]."'");
		}
		return $result;
	}
	function addresume_action(){
		if($_POST['next']){
			if($_POST['uid']){
				$this->obj->update_once('resume',array('name'=>trim($_POST['resume_name']),'sex'=>$_POST['sex'],'birthday'=>$_POST['birthday'],'living'=>$_POST['living'],'edu'=>$_POST['edu'],'exp'=>$_POST['exp'],'telphone'=>trim($_POST['moblie']),'email'=>trim($_POST['email']),'description'=>trim($_POST['description'])),array('uid'=>$_POST['uid']));
				$this->obj->update_once('member',array('email'=>trim($_POST['email']),'moblie'=>trim($_POST['moblie'])),array('uid'=>$_POST['uid']));
				echo "<script type='text/javascript'>window.location.href='index.php?m=admin_resume&c=saveresume&uid=".$_POST['uid']."'</script>";die;
			}else{
				if($this->config["sy_uc_type"]=="uc_center"){
					$this->obj->uc_open();
					$user = uc_get_user($_POST['username']);
				}else{
					$user = $this->obj->DB_select_once("member","`username`='".$_POST['username']."'","`uid`");
				}
				$password=trim($_POST['password']);
				if(is_array($user)){
					$this->obj->ACT_layer_msg("该会员已经存在！",8,"index.php?m=user_member&c=add",2);die;
				}else{
					$time = time();
					$ip = $this->obj->fun_ip_get();
					if($this->config["sy_uc_type"]=="uc_center"){
						$uid=uc_user_register($_POST['username'],$password,$_POST['email']);
						if($uid<0){
							$this->obj->get_admin_msg("index.php?m=com_member&c=add","该邮箱已存在！");
						}else{
							list($uid,$username,$email,$password,$salt)=uc_get_user($_POST['username'],$password);
							$value = "`username`='".$_POST['username']."',`password`='$password',`email`='".$_POST['email']."',`usertype`='1',`salt`='$salt',`moblie`='".$_POST['moblie']."',`reg_date`='$time',`reg_ip`='$ip'";
						}
					}else{
						$salt = substr(uniqid(rand()), -6);
						$pass = md5(md5($password).$salt);
						$value = "`username`='".$_POST['username']."',`password`='$pass',`email`='".$_POST['email']."',`usertype`='1',`status`='1',`salt`='$salt',`moblie`='".$_POST['moblie']."',`reg_date`='$time',`reg_ip`='$ip'";
					}
					$nid = $this->obj->DB_insert_once("member",$value);
					if($nid>0){
						$this->obj->DB_insert_once("resume","`uid`='$nid',`email`='".$_POST['email']."',`telphone`='".$_POST['moblie']."',`name`='".$_POST['resume_name']."',`description`='".$_POST['description']."',`sex`='".$_POST['sex']."',`living`='".$_POST['living']."',`exp`='".$_POST['exp']."',`edu`='".$_POST['edu']."',`birthday`='".$_POST['birthday']."'");
						$this->obj->DB_insert_once("member_statis","`uid`='$nid'");
						$this->obj->DB_insert_once("friend_info","`uid`='$nid',`nickname`='".$_POST['resume_name']."',`usertype`='1'");
						echo "<script type='text/javascript'>window.location.href='index.php?m=admin_resume&c=saveresume&uid=".$nid."&myself=".$_POST['myself']."'</script>";die;
					}else{
						$this->obj->ACT_layer_msg("会员添加失败，请重试！",8,"index.php?m=user_member&c=add",2);die;
					}
				}
			}
		}else{
			$CacheArr['user'] =array('userdata','userclass_name');
			$this->CacheInclude($CacheArr);
			$row=$this->obj->DB_select_once("resume","`uid`='".$_GET['uid']."'");
			$this->yunset("row",$row);
			$this->yuntpl(array('admin/admin_addresume'));
		}
	}
	function saveresume_action(){
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$arr=$this->CacheInclude($CacheArr);
		if($_GET['e']){
			$eid=(int)$_GET['e'];
			$row=$this->obj->DB_select_once("resume_expect","id='".$eid."' AND `uid`='".$_GET['uid']."'");
			if(!is_array($row) || empty($row))
			{
				$this->obj->ACT_msg("index.php?c=resume","无效的简历！");
			}
			$job_classid=@explode(",",$row['job_classid']);
			if(is_array($job_classid)){
				foreach($job_classid as $key){
					$job_classname[]=$arr['job_name'][$key];
				}
				$this->yunset("job_classname",$this->pylode(' ',$job_classname));
				$this->yunset("job_classname2",$this->pylode(',',$job_classname));
			}
			$this->yunset("job_classid",$job_classid);
			$this->yunset("row",$row);
			//专业技能
			$skill = $this->obj->DB_select_all("resume_skill","`eid`='".$eid."' AND `uid` = '".$_GET['uid']."'");
			$this->yunset("skill",$skill);
			//工作经历
			$work = $this->obj->DB_select_all("resume_work","`eid`='".$eid."' AND `uid` = '".$_GET['uid']."'");
			$this->yunset("work",$work);
			//项目经历
			$project = $this->obj->DB_select_all("resume_project","`eid`='".$eid."' AND `uid` = '".$_GET['uid']."'");
			$this->yunset("project",$project);
			//教育经历
			$edu = $this->obj->DB_select_all("resume_edu","`eid`='".$eid."' AND `uid` = '".$_GET['uid']."'");
			$this->yunset("edu",$edu);
			//培训经历
			$training = $this->obj->DB_select_all("resume_training","`eid`='".$eid."' AND `uid` = '".$_GET['uid']."'");
			$this->yunset("training",$training);
			//证书
			$cert = $this->obj->DB_select_all("resume_cert","`eid`='".$eid."' AND `uid` = '".$_GET['uid']."'");
			$this->yunset("cert",$cert);
			//其他
			$other = $this->obj->DB_select_all("resume_other","`eid`='".$eid."' AND `uid` = '".$_GET['uid']."'");
			$this->yunset("other",$other);
		}
		$resume=$this->obj->DB_select_once("resume","`uid`='".$_GET['uid']."'");
		$this->yunset("resume",$resume);
		$this->yunset("uid",$_GET['uid']);
		$this->yunset("myself",$_GET['myself']);
		if($_GET['return_url']){
			$this->yunset("return_url",'myresume');
		}else{
			$this->yunset("return_url",'resume');
		}
		$this->yuntpl(array('admin/admin_saveresume'));
	}
	function saveexpect_action(){
		if($_POST['submit']){
			$eid=(int)$_POST['eid'];
			unset($_POST['submit']);
			unset($_POST['eid']);
			unset($_POST['pytoken']);
			unset($_POST['urlid']);
			$_POST['name'] = iconv("utf-8", "gbk", $_POST['name']);
			$where['id']=$eid;
			$where['uid']=$_POST['uid'];
			$_POST['lastupdate']=time();
			if($eid==""){
				$_POST['receive_status']='1';
				$resume_num=$this->obj->DB_select_num("resume_expect","`uid`='".$_POST['uid']."'","id");
				if($resume_num>=$this->config['user_number']){echo 1;die;}
				$nid=$this->obj->insert_into("resume_expect",$_POST);
				if ($nid){
					$num=$this->obj->DB_select_once("member_statis","`uid`='".$_POST['uid']."'");
					if($num['resume_num']==0){
						$this->obj->update_once('resume',array('def_job'=>$nid),array('uid'=>$_POST['uid']));
					}
					$data['uid'] = $_POST['uid'];
					$data['eid'] = $nid;
					$this->obj->insert_into("user_resume",$data);
					$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`+1","`uid`='".$_POST['uid']."'");
					$state_content = "发布了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$nid\" target=\"_blank\">新简历</a>。";
					$fdata['uid']	  = $_POST['uid'];
					$fdata['content'] = $state_content;
					$fdata['ctime']   = time();
					$this->obj->insert_into("friend_state",$fdata);
				}
				$eid=$nid;
			}else{
				$nid=$this->obj->update_once("resume_expect",$_POST,$where);
			}
			$row=$this->obj->DB_select_once("user_resume","`expect`='1'","`eid`='$eid'");
			$this->obj->update_once('user_resume',array('expect'=>1),array('eid'=>$eid,'uid'=>$_POST['uid']));
			if($nid){
				$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
				$resume=$this->obj->DB_select_once("resume_expect","`id`='".$eid."'");
 				include APP_PATH."/plus/user.cache.php";
				include APP_PATH."/plus/job.cache.php";
				include APP_PATH."/plus/city.cache.php";
				include APP_PATH."/plus/industry.cache.php";
				$resume['report']=$userclass_name[$resume['report']];
				$resume['hy']=$industry_name[$resume['hy']];
				$resume['city']=$city_name[$resume['provinceid']]." ".$city_name[$resume['cityid']]." ".$city_name[$resume['three_cityid']];
				$resume['salary']=$userclass_name[$resume['salary']];
				$resume['type']=$userclass_name[$resume['type']];
				if($resume['job_classid']!=""){
					$job_classid=@explode(",",$resume['job_classid']);
					foreach($job_classid as $v){
						$job_classname[]=$job_name[$v];
					}
					$resume['job_classname']=$this->pylode(" ",$job_classname);
				}
				$resume['three_cityid']=$city_name[$resume['three_cityid']];

				if(is_array($resume)){
					foreach($resume as $k=>$v)
					{
						$arr[$k]=iconv("gbk","utf-8",$v);
					}
				}
				echo json_encode($arr);die;
			}else{
				echo 0;die;
			}
		}
	}
	function pylode($string,$array){
		$str = @implode($string,$array);
		if(!preg_match("/^[0-9,]+$/",$str) && $string==','){
			$str = 0;
		}
		return $str;
	}
	function skill_action(){
		$this->resume("resume_skill","skill","expect","填写项目经验");
	}
	function work_action()
	{
		$this->resume("resume_work","work","expect","填写项目经验");
	}
	function project_action()
	{
		$this->resume("resume_project","project","edu","填写教育经历");
	}
	function edu_action()
	{
		$this->resume("resume_edu","edu","training","填写培训经历");
	}
	function training_action()
	{
		$this->resume("resume_training","training","cert","填写证书");
	}
	function cert_action()
	{
		$this->resume("resume_cert","cert","other","填写其它");
	}
	function other_action()
	{
		$this->resume("resume_other","other","resume","返回简历管理");
	}
	function resume($table,$url,$nexturl,$name=""){
       if($_POST["submit"]){
			$eid=(int)$_POST['eid'];
			$id=(int)$_POST['id'];
			$this->uid=$_POST['uid'];
			unset($_POST['submit']);
			unset($_POST['id']);
			unset($_POST['table']);
			if($_POST['name'])$_POST['name'] = iconv("utf-8", "gbk", $_POST[name]);
			if($_POST['content'])$_POST['content'] = iconv("utf-8", "gbk", $_POST['content']);
			if($_POST['title'])$_POST['title'] = iconv("utf-8", "gbk", $_POST['title']);
			if($_POST['department'])$_POST['department'] = iconv("utf-8", "gbk", $_POST['department']);
			if($_POST['sys'])$_POST['sys'] = iconv("utf-8", "gbk", $_POST['sys']);
			if($_POST['specialty'])$_POST['specialty'] = iconv("utf-8", "gbk", $_POST['specialty']);
			if($_POST['sdate']){
				$_POST['sdate']=strtotime($_POST['sdate']);
			}
			if($_POST['edate']){
				$_POST['edate']=strtotime($_POST['edate']);
			}
			if(!$id){
				$nid=$this->obj->insert_into($table,$_POST);
				$this->obj->DB_update_all("user_resume","`$url`=`$url`+1","`eid`='$eid' and `uid`='".$this->uid."'");
				if($nid){
					$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
					$this->select_resume($table,$nid,$numresume);
				}else{
					echo 0;die;
				}
			}else{
				unset($_POST['uid']);
				$where['id']=$id;
				$nid=$this->obj->update_once($table,$_POST,$where);
				if($nid){
					$this->select_resume($table,$id);
				}else{
					echo 0;die;
				}
			}
		}
		$rows=$this->obj->DB_select_all($table,"`eid`='".$_GET['e']."'");
		$this->yunset("rows",$rows);
	}
	function select_resume($table,$id,$numresume=""){
		include APP_PATH."/plus/user.cache.php";
		$info=$this->obj->DB_select_once($table,"`id`='".$id."'");
		$info['skillval']=$userclass_name[$info['skill']];
		$info['ingval']=$userclass_name[$info['ing']];
		$info['sdate']=date("Y-m-d",$info['sdate']);
		$info['edate']=date("Y-m-d",$info['edate']);
		$info['numresume']=$numresume;
		if(is_array($info))
		{
			foreach($info as $k=>$v){
				$arr[$k]=iconv("gbk","utf-8",$v);
			}
		}
		echo json_encode($arr);die;
	}
	function resume_ajax_action(){
		include APP_PATH."/plus/user.cache.php";
		$table="resume_".$_POST['type'];
		$id=(int)$_POST['id'];
		$info=$this->obj->DB_select_once($table,"`id`='".$id."'");
		$info['skillval']=$userclass_name[$info['skill']];
		$info['ingval']=$userclass_name[$info['ing']];
		$info['sdate']=date("Y-m-d",$info['sdate']);
		$info['edate']=date("Y-m-d",$info['edate']);
		if(is_array($info)){
			foreach($info as $k=>$v){
				$arr[$k]=iconv("gbk","utf-8",$v);
			}
		}
		echo json_encode($arr);die;
	}
	function resume_del_action(){
		if($_POST['id']&&$_POST['table']){
			$tables=array("skill","work","project","edu","training","cert","other");
			if(in_array($_POST['table'],$tables)){
				$table = "resume_".$_POST['table'];
				$eid=(int)$_POST['eid'];
				$id=(int)$_POST['id'];
				$uid=(int)$_POST['uid'];
				$url = $_POST['table'];
				$nid=$this->obj->DB_delete_all($table,"`id`='".$id."' and `uid`='".$uid."'");
				$this->obj->DB_update_all("user_resume","`".$url."`=`".$url."`-1","`eid`='".$eid."' and  `uid`='".$uid."'");
				$resume=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
				$resume[$url];
				if($nid){
					$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
					$numresume=$this->obj->complete($resume_row);
					echo $numresume."##".$resume[$url];;die;
				}else{
					echo 0;die;
				}
			}else{
				echo 0;die;
			}
		}
	}
	function check_username_action(){
		$username=iconv("utf-8", "gbk", $_POST['username']);
		$member=$this->obj->DB_select_once("member","`username`='".$username."'","`uid`");
		echo $member['uid'];die;
	}
}
?>