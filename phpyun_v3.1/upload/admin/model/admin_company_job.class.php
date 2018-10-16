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
class admin_company_job_controller extends common{
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
		$where .=" 1 ";
        if ($_GET['news_search']){
        	extract($_GET);
        	if ($salary!=""){
        		$where .=" and `salary`=$salary";
        		$urlarr['salary']=$salary;
        	}else{

        	}
        	if ($job_type!=""){
        		$where .=" and `type`=$job_type";
        		$urlarr['job_type']=$job_type;
        	}
	         if($key!="")
			{
				if($type=='1'){
					$where .=" and  `com_name` like '%".$key."%'";
				}elseif($type=='2'){
					$where .=" and `name` like '%".$key."%'";
				}
				$urlarr['type']=$type;
				$urlarr['key']=$_GET['key'];
			}
			$urlarr['news_search']=$_GET['news_search'];
		  }
		if($_GET['id'])
		{
			$where.=" and `id`='".$_GET['id']."'";
			$urlarr['id']=$_GET['id'];
		}
		if($_GET['state']){
			if($_GET['state']=="1"){
				$where .= " and `edate`>'".time()."' and `state`='1'";
			}elseif($_GET['state']=="2"){
				$where .= " and `edate`<'".time()."'";
			}elseif($_GET['state']=="3"){
				$where .= " and `state`='".$_GET['state']."'";
			}elseif($_GET['state']=="4"){
				$where .= " and `state`='0'";
			}
			$urlarr['state']=$_GET['state'];
		}

		if($_GET['rec']){
			$where .= " and `rec_time`>".time();
			$urlarr['rec']=$_GET['rec'];
		}
		if($_GET['urgent']){
			$where.= " and `urgent_time`>".time();
			$urlarr['urgent']=$_GET['urgent'];
 		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		if($_GET['advanced']){
			$where = $wheres;
			$urlarr['advanced']=$_GET['advanced'];
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("company_job",$where,$pageurl,$this->config['sy_listnum']);
		include APP_PATH."/plus/job.cache.php";
		include APP_PATH."/plus/industry.cache.php";
		include APP_PATH."/plus/com.cache.php";
		if(is_array($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]['edu']=$comclass_name[$v['edu']];
				$rows[$k]['exp']=$comclass_name[$v['exp']];
				if($v['job_post']){
					$rows[$k]['job']=$job_name[$v['job_post']];
				}else{
					$rows[$k]['job']=$job_name[$v['job1_son']];
				}
				$rows[$k]['salary']=$comclass_name[$v['salary']];
				$rows[$k]['type']=$comclass_name[$v['type']];
				if($v['urgent_time']>$time){
					$rows[$k]['urgent_day'] = ceil(($v['urgent_time']-$time)/86400);
				}else{
					$rows[$k]['urgent_day'] = "0";
				}
				if($v['rec_time']>$time){
					$rows[$k]['rec_day'] = ceil(($v['rec_time']-$time)/86400);
				}else{
					$rows[$k]['rec_day'] = "0";
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->job_cache();
		$this->com_cache();
		$this->industry_cache();
		$this->yunset("rows",$rows);
		$this->yunset("time",$time);
		$this->yuntpl(array('admin/admin_company_job'));
	}
	function show_action(){
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		if($_GET['id']){
			$show=$this->obj->DB_select_once("company_job","id='".$_GET['id']."'");
			$show['lang']=@explode(',',$show['lang']);
			$show['welfare']=@explode(',',$show['welfare']);
			$this->yunset("show",$show);
		}

		if($_POST['update']){
			$_POST['lang']=@implode(',',$_POST['lang']);
			$_POST['welfare']=@implode(',',$_POST['welfare']);
			$_POST['sdate']=strtotime($_POST['sdate']);
			$_POST['edate']=strtotime($_POST['edate']);
			$_POST['lastupdate'] = time();
			$_POST['description'] = str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			unset($_POST['update']);unset($_POST['content']);
			if($_POST['edate']>time()){
				$_POST['state']="1";
			}else{
				$this->obj->ACT_layer_msg("结束时间不能小于当前时间",8,"index.php?m=admin_company_job",2,1);
			}

			if($_POST['id']&&$_POST['uid']==''){
				$job=$this->obj->DB_select_once("company_job","`id`='".$_POST['id']."'","`uid`,`state`");
				if($job['state']==0){
					$value="`status0`=`status0`-1,`status1`=`status1`+1";
				}
				if($job['state']==2){
					$value="`status2`=`status2`-1,`status1`=`status1`+1";
				}
				if($job['state']==3){
					$value="`status3`=`status3`-1,`status1`=`status1`+1";
				}
				$this->obj->DB_update_all("company_statis",$value,"`uid`='".$job['uid']."'");
				$where['id']=$_POST['id'];
				unset($_POST['id']);
				$this->obj->update_once("company_job",$_POST,$where);
				$this->obj->ACT_layer_msg( "职位(ID:".$where['id'].")修改成功！",9,"index.php?m=admin_company_job",2,1);
			}else if($_POST['uid']){
				$statis=$this->obj->DB_select_once("company_statis","`uid`='".$_POST['uid']."'","`vip_etime`,`job_num`,`integral`");
				if($statis['vip_etime']<time()){
					if($statis['job_num']<1){
						if($this->config['com_integral_online']=="1"){
							if($statis['integral']<$this->config['integral_job']){
								$this->obj->ACT_layer_msg($this->config['integral_pricename']."不够发布职位！",8,"index.php?m=admin_company_job");
							}
						}else{
							$this->obj->ACT_layer_msg("会员发布职位用完,购买会员服务更快捷！",8,"index.php?m=admin_company_job");
						}
					}
				}
				$company=$this->obj->DB_select_once("company","`uid`='".$_POST['uid']."'","name");
				$_POST['com_name']=$company['name'];
				$id=$this->obj->insert_into("company_job",$_POST);
				if($id){
					if($this->config['com_job_status']){
						$value="`job_num`=`job_num`-1,`job`=`job`+1,`status1`=`status1`+1";
					}else{
						$value="`job_num`=`job_num`-1,`job`=`job`+1,`status0`=`status0`+1";
					}
					$this->obj->DB_update_all("company_statis",$value,"`uid`='".$_POST['uid']."'");
					$this->obj->ACT_layer_msg( "职位(ID:".$id.")发布成功！",9,'index.php?m=admin_company_job&c=show&uid='.$_POST['uid'],2,1);
				}else{
					$this->obj->ACT_layer_msg( "职位发布失败！",8,'index.php?m=admin_company_job&c=show&uid='.$_POST['uid'],2,1);
				}
			}
		}
		$this->yunset("uid",$_GET['uid']);
		$this->yuntpl(array('admin/admin_company_job_show'));
	}
	function lockinfo_action(){
		$userinfo = $this->obj->DB_select_once("company_job","`id`=".$_POST['id'],"`statusbody`");
		echo $userinfo['statusbody'];die;
	}
	function status_action(){
		 extract($_POST);
		 $id = @explode(",",$pid);
		 if(is_array($id)){
			foreach($id as $value){
				$idlist[] = $value;
				$data[] = $this->shjobmsg($value,$status,$statusbody);
			}
			if($data!=""){
				$smtp = $this->email_set();
				foreach($data as $key=>$value){
					$this->send_msg_email($value['email'],$smtp);
				}
			}
			$aid = @implode(",",$idlist);
			$id=$this->obj->DB_update_all("company_job","`state`='$status',`statusbody`='".$statusbody."'","`id` IN ($aid)");
			$id?$this->obj->ACT_layer_msg("职位审核(ID:".$aid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",3,$_SERVER['HTTP_REFERER']);
		}
	}
	function saveclass_action(){
		extract($_POST);
		if($hy==""){
			$this->obj->ACT_layer_msg("请选择行业类别！",8,$_SERVER['HTTP_REFERER']);
		}
		if($job1==""){
			$this->obj->ACT_layer_msg("请选择职位类别！",8,$_SERVER['HTTP_REFERER']);
		}
		$id=$this->obj->DB_update_all("company_job","`hy`='$hy',`job1`='$job1',`job1_son`='$job1_son',`job_post`='$job_post'","`id` IN ($jobid)");
		$id?$this->obj->ACT_layer_msg("职位类别(ID:".$jobid.")修改成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("修改失败！",8,$_SERVER['HTTP_REFERER']);
	}
	function jobclass_action(){
		include(PLUS_PATH."industry.cache.php");
		include(PLUS_PATH."job.cache.php");
		if(is_array($job_type[$_POST['val']])&&$job_type[$_POST['val']]){
			foreach($job_type[$_POST['val']] as $val){
				$html.="<option value='".$val."'>".$job_name[$val]."</option>";
			}
		}else{$html.="<option value=''>暂无分类</option>";}
		echo $html;
	}
	function shjobmsg($jobid,$yesid,$statusbody){
		$data=array();
		$comarr=$this->obj->DB_select_once("company_job","`id`='$jobid'","uid,name,state");
		if($comarr['state']==0 && $yesid==1){
			$value="`status0`=`status0`-1,`status1`=`status1`+1";
			$data['type']="zzshtg";
		}
		if($comarr['state']==0 && $yesid==3){
			$value="`status0`=`status0`-1,`status3`=`status3`+1";
			$data['type']="zzshwtg";
		}
		if($comarr['state']==1 && $yesid==3){
			$value="`status1`=`status1`-1,`status3`=`status3`+1";
			$data['type']="zzshwtg";
		}
		if($comarr['state']==1 && $yesid==0){
			$value="`status1`=`status1`-1,`status0`=`status0`+1";
			$data['type']="";
		}
		if($comarr['state']==3 && $yesid==1){
			$value="`status3`=`status3`-1,`status1`=`status1`+1";
			$data['type']="zzshtg";
		}
		if($comarr['state']==3 && $yesid==0){
			$value="`status3`=`status3`-1,`status0`=`status0`+1";
			$data['type']="";
		}
		if($value!=""){
			$this->obj->DB_update_all("company_statis",$value,"`uid`='".$comarr['uid']."'");
			if($data['type']!=""){
				$uid=$this->obj->DB_select_once("member","`uid`='".$comarr['uid']."'","email,moblie");
				$data['email']=$uid['email'];
				$data['moblie']=$uid['moblie'];
				$data['jobname']=$comarr['name'];
				$data['date']=date("Y-m-d H:i:s");
				$data['status_info']=$statusbody;
				return $data;
			}
		}
	}
	function ctime_action(){
		extract($_POST);
		$id=@explode(",",$jobid);
		if(is_array($id)){
			$posttime=$endtime*86400;
			foreach($id as $value){
				$row=$this->obj->DB_select_once("company_job","`id`='".$value."'");
				$time=$row['edate']+$posttime;
				if($row['state']==2 && $time>time()){
					$id=$this->obj->DB_update_all("company_job","`edate`='".$time."',`state`='1'","`id`='".$value."'");
					$this->obj->DB_update_all("company_statis","`status2`=`status2`-1,`status1`=`status1`+1","`uid`='".$row['uid']."'");
				}else{
					$id=$this->obj->DB_update_all("company_job","`edate`='".$time."'","`id`='".$value."'");
				}
			}
			$id?$this->obj->ACT_layer_msg("职位延期(ID:".$jobid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",3,$_SERVER['HTTP_REFERER']);
		}
 	}
	function recommend_action(){
		extract($_POST);
		if($pid){
			$addtime = 86400*$addday;
			if($s==1){
				$this->obj->DB_update_all("company_job","`rec_time`='0',`rec`='0'","`id`='$pid'");
			}elseif($eid>time()){
				$this->obj->DB_update_all("company_job","`rec_time`=`rec_time`+$addtime,`rec`='1'","`id`='$pid'");
			}else{
				$this->obj->DB_update_all("company_job","`rec_time`=".time()."+$addtime,`rec`='1'","`id`='$pid'");
			}
		}
		$this->obj->ACT_layer_msg("职位推荐(ID:".$pid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
	}
	function urgent_action(){
		extract($_POST);
		if($pid){
			$addtime = 86400*$addday;
			if($s==1){
				$this->obj->DB_update_all("company_job","`urgent_time`='0',`urgent`='0'","`id`='$pid'");
			}elseif($eid>time()){
				$this->obj->DB_update_all("company_job","`urgent_time`=`urgent_time`+$addtime,`urgent`='1'","`id`='$pid'");
			}else{
				$this->obj->DB_update_all("company_job","`urgent_time`=".time()."+$addtime,`urgent`='1'","`id`='$pid'");
			}
		}
		$this->obj->ACT_layer_msg("紧急招聘(ID:".$pid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
	    			$layer_type=1;
			    	foreach($del as $v){
			    	    $this->del_com($v);
			    	}
					$del_uid=@implode(',',$del);
		    	}else{
		    		$layer_type=0;
					$del_uid=$del;
		    		$this->del_com($del);
		    	}
				$this->layer_msg( "职位(ID:".$del_uid.")删除成功！",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->del_com($_GET['id']);
			isset($result)?$this->layer_msg('职位(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function del_com($id){
		$id_arr = @explode("-",$id);
		if($id_arr[0]){
			$rows=$this->obj->DB_select_once("company_job","`id`='".$id_arr[0]."' and `r_status`<>'2'","`state`");
			$result=$this->obj->DB_delete_all("company_job","`id`='".$id_arr[0]."'" );
			$this->obj->DB_delete_all("company_job_link","`jobid`='".$id_arr[0]."'" );
			$status0=$status1=$status2=$status3=0;
			if($rows['state']=="0"){
				$status0=$status0+1;
			}elseif($rows['state']=="1"){
				$status1=$status1+1;
			}elseif($rows['state']=="2"){
				$status2=$status2+1;
			}elseif($rows['state']=="3"){
				$status3=$status3+1;
			}
			$value.="`status0`=`status0`-$status0,";
			$value.="`status1`=`status1`-$status1,";
			$value.="`status2`=`status2`-$status2,";
			$value.="`status3`=`status3`-$status3,";
			$value.="`job`=`job`-1";
			$this->obj->DB_update_all("company_statis",$value,"`uid`='".$id_arr[1]."'");
		}
		return $result;
	}
	function refresh_action()
	{
		$list=$this->obj->DB_select_all("company_job","`id` in (".$_POST['ids'].")","`uid`");
		if(is_array($list))
		{
			foreach($list as $v)
			{
				$uid[]=$v['uid'];
			}
			$this->obj->DB_update_all("company","`jobtime`='".time()."'","`uid` in (".@implode(",",$uid).")");
		}
		$this->obj->DB_update_all("company_job","`lastupdate`='".time()."'","`id` in (".$_POST['ids'].")");
	}
}
?>