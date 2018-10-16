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
	function public_action(){
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("now_url",$now_url);
		$this->yunset("comstyle","../template/member/com");
		include(PLUS_PATH."menu.cache.php");
		$this->yunset("menu_name",$menu_name);
	}
	function logout_action()
	{
		$this->logout();
	}
	function company_satic()
	{
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");

		if($statis['vip_etime']<time()){
			$rating=$this->obj->DB_select_once("company_rating","`id`='".$statis['rating']."'");
			if($rating['type']=='1'){
				$nums=$statis['job_num']+$statis['editjob_num']+$statis['breakjob_num']+$statis['down_resume'];
			}else{
				$nums=0;
			}
			if($nums<1){
				$data['rating_name']="非会员";
				$data['rating']="";
				$data['vip_etime']="";
				$where['uid']=$this->uid;
				$this->obj->update_once("company_statis",$data,$where);
			}
		}
		if($statis['autotime']>=time()){

			$statis['auto'] = 1;
		}
		$this->yunset("statis",$statis);
		return $statis;
	}
	function get_com($type){
		$statis=$this->company_satic();

		if($statis['rating']){
			if($type==1){
				if($statis['vip_etime']<time()){
					if($statis['job_num']){
						$value="`job_num`=`job_num`-1";
					}else{
						if($this->config['com_integral_online']=="1"){
							$this->intergal($type,$statis);
						}else{
							$this->obj->ACT_layer_msg("会员发布职位用完,购买会员服务更快捷！",8,"index.php?c=job&w=1");
						}
					}
				}
			}elseif($type==2){
				if($statis['vip_etime']<time()){
					if($statis['editjob_num']){
						$value="`editjob_num`=`editjob_num`-1";
					}else{
						if($this->config['com_integral_online']=="1"){
							$this->intergal($type,$statis);
						}else{
							$this->obj->ACT_layer_msg("会员修改职位用完！",8,"index.php?c=job&w=1");
						}
					}
				}
			}elseif($type==3){
				if($statis['vip_etime']<time()){
					if($statis['breakjob_num']>0){
						$value="`breakjob_num`=`breakjob_num`-1";
					}else{
						 if($this->config['com_integral_online']=='1'){
							$this->intergal($type,$statis);
						}else if($this->config['com_integral_online']=='2'){
							$this->layer_msg("特权已到期，请重新购买！",8,0,"index.php?c=pay");
						}
					}
				}
			}
			if($value){
				$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
			}
		}else{
			$this->intergal($type,$statis);
		}
	}
	function intergal($type,$statis){
		if($type==1 && $this->config['integral_job']){
			if($statis['integral']<$this->config['integral_job']){
				$this->obj->ACT_layer_msg("你的".$this->config['integral_pricename']."不够发布职位！",8,"index.php?c=pay");
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_job'],false,"发布职位");
		}elseif($type==2 && $this->config['integral_jobedit']){
			if($statis['integral']<$this->config['integral_jobedit'])
			{
				$this->obj->ACT_layer_msg("你的".$this->config['integral_pricename']."不够修改职位！",8,"index.php?c=pay");
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_jobedit'],false,"修改职位");
		}elseif($type==3 && $this->config['integral_jobefresh']){
			if($statis['integral']<$this->config['integral_jobefresh']){
				if($_GET){
					$this->layer_msg("你的".$this->config['integral_pricename']."不够刷新职位！",8,0,"index.php?c=pay");
				}else{
					$this->obj->ACT_layer_msg("你的".$this->config['integral_pricename']."不够刷新职位！",8,"index.php?c=pay");
				}
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_jobefresh'],false,"刷新职位");
		}
	}
	function com_tpl($tpl){
		$this->yuntpl(array('member/com/'.$tpl));
	}
	function index_action(){
		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);
		$this->company_satic();
		$rows=$this->obj->DB_select_all("company_order","`uid`='".$this->uid."' order by id desc limit 10","`type`,`order_price`,`order_state`,`order_remark`,`order_time`,`order_type`");
		if(is_array($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]["order_state"]=$arr_data["paystate"][$v["order_state"]];
			}
		}
		$this->yunset("rows",$rows);
		$rows_one=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."' and `state`='1' and `r_status`<>'2' order by id desc limit 10","`id`,`name`,`lastupdate`,`sdate`,`jobhits`");
		$rows_two=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."' and `state`='0' and `r_status`<>'2' order by id desc limit 10","`id`,`name`,`lastupdate`,`sdate`,`jobhits`");
		$rows_three=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."' and `state`='2' and `r_status`<>'2' order by id desc limit 10","`id`,`name`,`lastupdate`,`sdate`,`jobhits`");
		$invite_resume=$this->obj->DB_select_num("userid_msg","`fid`='".$this->uid."'");
		$down_resume=$this->obj->DB_select_num("down_resume","`comid`='".$this->uid."'");
		$de_resume=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."' and `is_browse`='1'");
        $this->yunset("de_resume",$de_resume);
		$des_resume=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."'");
        $this->yunset("des_resume",$des_resume);
		$this->yunset("invite_resume",$invite_resume);
		$this->yunset("down_resume",$down_resume);
		$this->yunset("rows_one",$rows_one);
		$this->yunset("rows_two",$rows_two);
		$this->yunset("rows_three",$rows_three);
		$statis=$this->obj->DB_select_alls("company","company_statis","a.`uid`=b.`uid` and a.`uid`='".$this->uid."'","a.linkphone,a.`name`,a.`logo`,a.`cert`,b.*");

		if($statis[0]['rating']>0){
			$company_rating=$this->obj->DB_select_once("company_rating","`id`='".$statis[0]['rating']."'");
			if($statis[0]['vip_etime']>time()){
				$days=round(($statis[0]['vip_etime']-mktime())/3600/24) ;
				$this->yunset("days",$days);
			}
			$this->yunset("company_rating",$company_rating);
		}

		$this->yunset("statis",$statis[0]);
		$this->public_action();
		$this->yunset("js_def",1);
		$this->com_tpl('index');
	}
	function get_user()
	{
		$rows=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		if(!$rows['name'] || !$rows['address'] || !$rows['pr']){
			$this->obj->ACT_msg("index.php?c=info","请先完善公司资料！");
		}
		return $rows;
	}
	function jobadd_action()
	{
		$company=$this->get_user();

		$rows=$this->obj->DB_select_all("company_cert","`uid`='".$this->uid."' group by type order by id desc");
		foreach($rows as $v)
		{
			$row[$v['type']]=$v;
		}
		$msg=array();
		$isallow_addjob="1";
		$url="index.php?c=cert";
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
			$this->obj->ACT_msg($url,"请先完成".$this->pylode("、",$msg)."！");
		}

		if($_POST['submitBtn']){
			$id=intval($_POST['id']);
			$state= intval($_POST['state']);
			unset($_POST['submitBtn']);
			unset($_POST['id']);
			unset($_POST['state']);
			$_POST['uid']=$this->uid;
			$_POST['lastupdate']=time();
			$_POST['state']=$this->config['com_job_status'];
			$_POST['description'] = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'background-color:','background-color:','white-space:'),html_entity_decode($_POST['description'],ENT_QUOTES,"GB2312"));
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
			if($_POST['sdate']){
				$_POST['sdate']=strtotime($_POST['sdate']);
			}else{$_POST['sdate']=mktime();}
			if(trim($_POST['days'])&&$_POST['days_type']==''){
				$sdate=$_POST['sdate']+(int)trim($_POST['days'])*86400;
				$_POST['edate']=date('Y-m-d',$sdate);
				unset($_POST['days']);
			}else if($_POST['days_type']){unset($_POST['days_type']);unset($_POST['days']);}

			if($_POST['edate']){
				$_POST['edate']=strtotime($_POST['edate']);
				if($_POST['edate']<time()){
					$this->obj->ACT_layer_msg("结束时间小于当前日期，提交失败！",8,$_SERVER['HTTP_REFERER']);
				}
			}
			$com=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
			$satic=$this->company_satic();
			$_POST['com_name']=$company['name'];
			$_POST['com_logo']=$company['logo'];
			$_POST['com_provinceid']=$company['provinceid'];
			$_POST['pr']=$company['pr'];
			$_POST['mun']=$company['mun'];
			$_POST['rating']=$satic['rating'];
			$is_link=(int)$_POST['is_link'];
			$link_type=(int)$_POST['link_type'];
			$is_email=(int)$_POST['is_email'];
			$email_type=(int)$_POST['email_type'];
			$link_moblie=$_POST['link_moblie'];
			$email=$_POST['email'];
			$link_man=$_POST['link_man'];
			unset($_POST['is_email']);
			unset($_POST['link_moblie']);
			unset($_POST['email_type']);
			unset($_POST['link_man']);
			unset($_POST['email']);
			$where['id']=$id;
			$where['uid']=$this->uid;
			if(!$id){
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
				$rows=$this->obj->DB_select_once("company_job","`id`='".$id."' and `uid`='".$this->uid."'");
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

			if($is_link=='1'){
				if($link_type=='2'){
					$linkman=trim($link_man);
					$linktel=trim($link_moblie);
				}else{
					$linkman=$company['linkman'];
					$linktel=$company['linktel'];
				}
			}
			if($is_email && $email_type==2){
				$linkmail=trim($email);
			}else{
				$linkmail=$company['linkmail'];
			}
			$job_link="";
			if($is_link=="1" && $link_type==2){
				$job_link.="`link_man`='".$linkman."',";
				$job_link.="`link_moblie`='".$linktel."',";
			}
			$job_link.="`email_type`='".$email_type."',";
			$job_link.="`is_email`='".$is_email."',";
			$job_link.="`email`='".$linkmail."'";
			if($id){
				$linkid=$this->obj->DB_select_once("company_job_link","`uid`='".$this->uid."' and `jobid`='".$id."'","id");
				if($linkid['id']){
					$this->obj->DB_update_all("company_job_link",$job_link,"`id`='".$linkid['id']."'");
				}else{
					$job_link.=",`uid`='".$this->uid."',`jobid`='".(int)$id."'";
					$this->obj->DB_insert_once("company_job_link",$job_link);
				}
			}else if($nid>0){
				$job_link.=",`uid`='".$this->uid."',`jobid`='".(int)$nid."'";
				$this->obj->DB_insert_once("company_job_link",$job_link);
			}
 			$nid?$this->obj->ACT_layer_msg($name."成功！",9,"index.php?c=job&w=1"):$this->obj->ACT_layer_msg($name."失败！",8,$_SERVER['HTTP_REFERER']);
		}
		$this->public_action();
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		if($_GET['edit'] || $_GET['jobcopy'])
		{
			if($_GET['edit']){
				$id=(int)$_GET['edit'];
			}else{
				$id=(int)$_GET['jobcopy'];
			}
			$row=$this->obj->DB_select_once("company_job","`id`='".$id."' and `uid`='".$this->uid."'");
			$job_link=$this->obj->DB_select_once("company_job_link","`jobid`='".$id."' and `uid`='".$this->uid."'");
			if($row['lang']!="")
			{
				$row['lang']= @explode(",",$row['lang']);
			}
			if($row['welfare']!="")
			{
				$row['welfare']= @explode(",",$row['welfare']);
			}
			$row['days']= ceil(($row['edate']-$row['sdate'])/86400);
			$this->yunset("company",$company);
			$this->yunset("job_link",$job_link);
			$this->yunset("row",$row);
		}else{
			$row['hy']=$company['hy'];
			$row['sdate']=mktime();
			$row['edate']=strtotime("+1 month");
			$row['number']=$CacheArr['comdata']['job_number'][0];
			$row['salary']=$CacheArr['comdata']['job_salary'][0];
			$row['type']=$CacheArr['comdata']['job_type'][0];
			$row['exp']=$CacheArr['comdata']['job_exp'][0];
			$row['report']=$CacheArr['comdata']['job_report'][0];
			$row['age']=$CacheArr['comdata']['job_age'][0];
			$row['sex']=$CacheArr['comdata']['job_sex'][0];
			$row['edu']=$CacheArr['comdata']['job_edu'][0];
			$row['marriage']=$CacheArr['comdata']['job_marriage'][0];
			$this->yunset("company",$company);
			$this->yunset("row",$row);
		}

		$this->yunset("today",date('Y-m-d',time()));
		$this->yunset("js_def",3);
		$this->com_tpl('jobadd');
	}
	function job(){
		if($_GET['p_uid']){
			$data['p_uid']=(int)$_GET['p_uid'];
			$data['inputtime']=mktime();
			$data['c_uid']=$this->uid;
			$data['usertype']=(int)$_COOKIE['usertype'];
			$haves=$this->obj->DB_select_once("blacklist","`p_uid`=".$data['p_uid']."  and `c_uid`=".$data['c_uid']." and `usertype`=".$data['usertype']."");
			if(is_array($haves)){
				$this->obj->layer_msg("该用户已在您黑名单中！",8,0,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->insert_into("blacklist",$data);
				$num=$this->obj->DB_select_num("userid_job","`uid`=".$data['p_uid']."  and `com_id`=".$data['c_uid']."");
				$this->obj->DB_delete_all("userid_job","`uid`=".$data['p_uid']."  and `com_id`=".$data['c_uid'].""," ");
				$this->obj->DB_update_all("member_statis","`sq_jobnum`=`sq_jobnum`-$num","`uid`='".$data['p_uid']."'");
				$nid?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_GET['r_uid']){
			if($_GET['r_reason']=="")
			{
				$this->obj->ACT_layer_msg("举报内容不能为空！",8,"index.php?c=down");
			}
			$data['p_uid']=(int)$_GET['r_uid'];
			$data['inputtime']=mktime();
			$data['c_uid']=$this->uid;
			$data['eid']=(int)$_GET['eid'];
			$data['r_name']=$_GET['r_name'];
			$data['usertype']=(int)$_COOKIE['usertype'];
			$data['username']=$this->username;
			$data['r_reason']=$_GET['r_reason'];
			$haves=$this->obj->DB_select_once("report","`p_uid`=".$data['p_uid']." and `c_uid`=".$data['c_uid']." and `usertype`=".$data['usertype']."","id");
			if(is_array($haves))
			{
				$this->obj->ACT_layer_msg("您已经举报过该用户！",8,"index.php?c=down");
			}else{
				$nid=$this->obj->insert_into("report",$data);
 				$nid?$this->obj->ACT_layer_msg("操作成功！",9,"index.php?c=down"):$this->obj->ACT_layer_msg("操作失败！",8,"index.php?c=down");
			}
		}
		if($_POST['recid'])
		{
			$id=(int)$_POST['recid'];
			if($_POST['day']<1){
				$this->obj->ACT_layer_msg("请正确填写推荐天数！",2,$_SERVER['HTTP_REFERER']);
			}
			$reow=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","integral");
			$job=$this->obj->DB_select_once("company_job","`id`='".$id."'","rec_time");
			if($job['rec_time']<time())
			{
				$time=time()+$_POST['day']*86400;
			}else{
				$time=$job['rec_time']+$_POST['day']*86400;
			}
			$integral=$this->config['com_recjob']*$_POST['day'];
			if($reow['integral']<$integral)
			{
				$this->obj->ACT_layer_msg("您的".$this->config['integral_pricename']."不足，请充值！",8,"index.php?c=pay");
			}else{
				$this->obj->company_invtal($this->uid,$integral,false,"发布推荐职位");
			}
			$data['rec']=1;
			$data['rec_time']=$time;
			$where['id']=$id;
			$where['uid']=$this->uid;
			$this->obj->update_once("company_job",$data,$where);
 			$this->obj->ACT_layer_msg("推荐成功！",9,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['urgentid'])
		{
			$id=(int)$_POST['urgentid'];
			if($_POST['day']<1)
			{
 				$this->obj->ACT_layer_msg("请正确填写紧急天数！",8,$_SERVER['HTTP_REFERER']);
			}
			$reow=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","integral");
			$integral=$this->config['com_urgent']*$_POST['day'];
			$job=$this->obj->DB_select_once("company_job","`id`='".$id."'","urgent_time");
			if($job['urgent_time']<time())
			{
				$time=time()+$_POST['day']*86400;
			}else{
				$time=$job['urgent_time']+$_POST['day']*86400;
			}
			if($reow['integral']<$integral)
			{
 				$this->obj->ACT_layer_msg("您的".$this->config['integral_pricename']."不足，请充值！",8,"index.php?c=pay");
			}else{
				$this->obj->company_invtal($this->uid,$integral,false,"发布紧急职位");
				$data['urgent']=1;
				$data['urgent_time']=$time;
				$where['id']=$id;
				$where['uid']=$this->uid;
				$this->obj->update_once("company_job",$data,$where);
 				$this->obj->ACT_layer_msg("发布紧急职位成功！",9,$_SERVER['HTTP_REFERER']);
			}
		}

		if($_GET['up']){
			$this->get_com(3);
			$data['lastupdate']=time();
			$where['id']=(int)$_GET['up'];;
			$where['uid']=$this->uid;
			$nid=$this->obj->update_once("company_job",$data,$where);
			$this->obj->update_once("company",$data,array("uid"=>$this->uid));
			$nid?$this->layer_msg('刷新职位成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('刷新失败！',8,0,$_SERVER['HTTP_REFERER']);
		}

		if($_POST['gotimeid'])
		{
			if($_POST['day']<1)
			{
 				$this->obj->ACT_layer_msg("请正确填写延期天数！",8);
			}else{
				$posttime=$_POST['day']*86400;
				$ids=@explode(",",$_POST['gotimeid']);
				if(is_array($ids))
				{
					foreach($ids as $value)
					{
						$where=array();$data=array();
						$row=$this->obj->DB_select_once("company_job","`id`='".(int)$value."' and `uid`='".$this->uid."'");
						$time=$row['edate']+$posttime;
						$where['id']=(int)$value;
						$where['uid']=$this->uid;
						if($row['state']==2 && $time>time())
						{
							$data['edate']=$time;
							$data['state']=1;
							$id=$this->obj->update_once("company_job",$data,$where);
							$this->obj->update_once("company_statis","`status2`=`status2`-1,`status1`=`status1`+1","uid='".$this->uid."'");
						}else{
							$id=$this->obj->update_once("company_job",array("edate"=>$time),$where);
						}
					}
				}
				isset($id)?$this->obj->ACT_layer_msg("延期成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("延期失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}

		if($_POST['status'] && $_POST['id'])
		{
			$where['id']=(int)$_POST['id'];
			$where['uid']=$this->uid;
			$nid=$this->obj->update_once("company_job",array("status"=>(int)$_POST['status']),$where);
			if($nid)
			{
				echo 1;die;
			}else{
				echo 2;die;
			}
		}

		if($_GET['del'] || is_array($_POST['checkboxid']))
		{
			if(is_array($_POST['checkboxid'])){
				$layer_type=1;
				$delid=$this->pylode(",",$_POST['checkboxid']);
			}else if($_GET['del']){
				$layer_type=0;
				$delid=@explode(",",$_GET['del']);
				$delid=$this->pyloade(",",$ids);
			}
			$rows=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."' and `id` in (".$delid.")","`state`");
			$nid=$this->obj->DB_delete_all("company_job","`uid`='".$this->uid."' and `id` in (".$delid.")"," ");
			$this->obj->DB_delete_all("company_job_link","`uid`='".$this->uid."' and `jobid` in (".$delid.")"," ");
			if($nid){
				if(is_array($rows)){
					$status0=$status1=$status2=$status3=0;
					foreach($rows as $v){
						if($v['state']=="0"){
							$status0=$status0+1;
						}elseif($v['state']=="1"){
							$status1=$status1+1;
						}elseif($v['state']=="2"){
							$status2=$status2+1;
						}elseif($v['state']=="3"){
							$status3=$status3+1;
						}
					}
					$num=count($rows);
					$value.="`status0`=`status0`-$status0,";
					$value.="`status1`=`status1`-$status1,";
					$value.="`status2`=`status2`-$status2,";
					$value.="`status3`=`status3`-$status3,";
					$value.="`job`=`job`-$num";
					$this->obj->DB_update_all("company_statis",$value,"uid='".$this->uid."'");
				}
				$newest=$this->obj->DB_select_once("company_job","`uid`='".$this->uid."' order by lastupdate DESC","`lastupdate`");
				$this->obj->update_once("company",array("jobtime"=>$newest['lastupdate']),array("uid"=>$this->uid));
				$this->layer_msg('删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']);
			}else{$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);}
		}
	}
	function report_action()
	{
		if($_POST['submit'])
		{
			if($_POST['r_reason']=="")
			{
				$this->obj->ACT_layer_msg("举报内容不能为空！",8,$_SERVER['HTTP_REFERER']);
			}
			$data['c_uid']=(int)$_POST['r_uid'];
			$data['inputtime']=mktime();
			$data['p_uid']=$this->uid;
			$data['usertype']=(int)$_COOKIE['usertype'];
			$data['eid']=(int)$_POST['eid'];
			$data['r_name']=$_POST['r_name'];
			$data['username']=$this->username;
			$data['r_reason']=$_POST['r_reason'];
			$haves=$this->obj->DB_select_once("report","`p_uid`='".$data['p_uid']."' and `c_uid`='".$data['c_uid']."' and `usertype`='".$data['usertype']."'");
			if(is_array($haves))
			{
				$this->obj->ACT_layer_msg("您已经举报过该用户！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->insert_into("report",$data);
				$nid?$this->obj->ACT_layer_msg("举报成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("举报失败！",8,"index.php?m=com&c=comapply&id=".$data['eid']);
			}
		}
	}
	function job_action(){
		$this->job();
		$this->public_action();
		$urlarr=array("c"=>"job","page"=>"{{page}}");
		if($_GET['w']>=0)
		{
			$where=" and state='".(int)$_GET['w']."'";
			$urlarr['w'] = $_GET['w'];
		}
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company_job","`uid`='".$this->uid."' ".$where,$pageurl,"10");
		if(is_array($rows) && !empty($rows)){
			foreach($rows as $v){
				$jobid[]=$v['id'];
				$job_post[]=$v['job_post'];
			}
		}
		$userrows=$this->obj->DB_select_alls("resume","resume_expect","a.`def_job`=b.`id` and a.`r_status`<>'2' and b.`job_post` in (".$this->pylode(',',$job_post).")");
		if(is_array($userrows)){
			foreach($userrows as $v){
				$usernum[$v['job_post']]++;
			}
		}
		$this->yunset("usernum",$usernum);
		if(is_array($jobid)){
			$jobrows=$this->obj->DB_select_all("userid_job","`job_id` in (".$this->pylode(',',$jobid).")","`job_id`");
		}
		if(is_array($jobrows)){
			foreach($jobrows as $v){
				$jobnum[$v['job_id']]++;
			}
		}
		$this->yunset("jobnum",$jobnum);
		$Field = "SUM(case when 1=1 then 1 else 0 end) as job,SUM(case when state='0' then 1 else 0 end) as status0,SUM(case when state='1' then 1 else 0 end) as status1,SUM(case when state='2' then 1 else 0 end) as status2,SUM(case when state='3' then 1 else 0 end) as status3";
		$status=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."'",$Field);
		$this->yunset("status",$status[0]);
		$this->obj->DB_update_all("company_statis","`job`='".$status[0]['job']."',`status0`='".$status[0]['status0']."',`status1`='".$status[0]['status1']."',`status2`='".$status[0]['status2']."',`status3`='".$status[0]['status3']."'","`uid`='".$this->uid."'");
		$urgent=$this->config['com_urgent'];
		$this->yunset("urgent",$urgent);
		$this->company_satic();
		$this->yunset("js_def",3);
		$this->com_tpl('job');
	}


	function search_action(){
		$this->public_action();
		$urlarr["c"]="search";
		$where="`uid`='".$this->uid."'";
		if($_GET['keyword']){
			$where.=" and name like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company_job",$where,$pageurl,"10","`id`,`name`,`state`,`sdate`,`edate`,`lastupdate`,`jobhits`");
		if(is_array($rows) && !empty($rows))
		{
			foreach($rows as $v)
			{
				$jobid[]=$v['id'];
			}
			$jobrows=$this->obj->DB_select_all("userid_job","`job_id` in (".$this->pylode(',',$jobid).")");
			if(is_array($jobrows))
			{
				foreach($jobrows as $v)
				{
					$jobnum[$v['job_id']]++;
				}
			}
		}
		$this->company_satic();
		$this->yunset("jobnum",$jobnum);
		$this->yunset("js_def",3);
		$this->com_tpl('search');
	}
	function pay_action()
	{
		$this->public_action();
		$statis=$this->company_satic();
		if($_POST['usertype']=='price')
		{
			$rows=$this->obj->DB_select_all("company_rating","`service_price`<>'' and `display`='1' and `category`=1 order by sort desc","name,service_price,id");
			$this->yunset("rows",$rows);
		}
		$this->yunset("statis",$statis);
		$this->yunset("js_def",4);
		$this->com_tpl('pay');
	}
	function bank_action()
	{
		if($this->config['bank']!=1)
		{
			$this->obj->ACT_layer_msg("后台还没有银行转帐功能，请联系管理员！",3,"index.php");
		}
		if(isset($_POST['banksub']))
		{
			$dingdan=mktime().rand(10000,99999);
			$order_bank=$_POST['bankname']."@%".$_POST['bank_user']."@%".$_POST['bank_odd'];
			$data['uid']=$this->uid;
			$data['order_id']=$dingdan;
			$data['order_price']=$_POST['bank_price'];
			$data['order_type']="bank";
			$data['order_time']=time();
			$data['order_state']=3;
			$data['order_remark']=$_POST['bank_remark'];
			$data['order_bank']=$order_bank;
			$data['type']=3;
			$company_bank=$this->obj->insert_into("company_order",$data);
			if($company_bank)
			{
 				$this->obj->ACT_layer_msg("您已提交成功，请等待管理员确认！",9,"index.php?c=bank");
			}
		}
		$this->public_action();
		$rows=$this->obj->DB_select_all("bank");
		$this->yunset("rows",$rows);
		$this->yunset("js_def",4);
		$this->com_tpl('bank');
	}
	function paylog_action(){
		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);
		$this->public_action();
		if($_GET['consume']=="ok")
		{
			$urlarr=array("c"=>"paylog","consume"=>"ok","page"=>"{{page}}");
			$pageurl=$this->url("index","index",$urlarr);
			$where="`com_id`='".$this->uid."'";
			$where.=" and `order_price`<0 order by pay_time desc";
			$rows = $this->get_page("company_pay",$where,$pageurl,"10");
			if(is_array($rows))
			{
				foreach($rows as $k=>$v)
				{
					$rows[$k]['pay_time']=date("Y-m-d H:i:s",$v['pay_time']);
				}
			}
			$this->yunset("rows",$rows);
			$this->yunset("ordertype","ok");
		}else{
			$urlarr=array("c"=>"paylog","page"=>"{{page}}");
			$pageurl=$this->url("index","index",$urlarr);
			$where="`uid`='".$this->uid."'";
			$where.=" and `order_price`>0 order by order_time desc";
			$this->get_page("company_order",$where,$pageurl,"10");
		}
		if($_POST["submit"]){
			if(trim($_POST['order_remark'])==""){
				$this->obj->ACT_layer_msg("备注不能为空！",8,$_SERVER['HTTP_REFERER']);
			}
			$nid=$this->obj->DB_update_all("company_order","`order_remark`='".trim($_POST['order_remark'])."'","`id`='".(int)$_POST['id']."' and `uid`='".$this->uid."'");
			$nid?$this->obj->ACT_layer_msg("修改成功！",9,"index.php?c=paylog"):$this->obj->ACT_layer_msg("修改失败！",8,"index.php?c=paylog");
		}
		$this->yunset("js_def",4);
		$this->com_tpl('paylog');
	}
	function paymanage_action()
	{
		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);
		$this->public_action();
		$urlarr=array("c"=>"paymanage","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$where="`uid`='".$this->uid."' and `order_state`='2' order by order_time desc";
		$this->get_page("company_order",$where,$pageurl,"10");
		$this->yunset("js_def",4);
		$this->com_tpl('paymanage');
	}
	function message_action(){
		if($_POST['add_message']){
			$data['content']=trim($_POST['content']);
			$data['fa_uid']=$this->uid;
			$data['username']=$this->username;
			$data['ctime']=mktime();
			$nid=$this->obj->insert_into("message",$data);
 			$nid?$this->obj->ACT_layer_msg("添加成功！",9,"index.php?c=seemessage"):$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=seemessage");
		}
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('message');
	}
	function seemessage_action(){
       if($_GET['del']){
			$nid=$this->obj->DB_delete_all("message","(`id`='".(int)$_GET['del']."' OR `keyid`='".(int)$_GET['del']."') AND `fa_uid`='".$this->uid."'"," ");
			$this->unset_remind("message2",'2');
 			$nid?$this->layer_msg('删除成功！',9,0,"index.php?c=seemessage"):$this->layer_msg('删除失败！',8,0,"index.php?c=seemessage");
		}
		$urlarr=array("c"=>"seemessage","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("message","(`fa_uid`='".$this->uid."' or `uid`='".$this->uid."') and `keyid`='0' order by id desc",$pageurl,"10");
		$this->public_action();
		$this->obj->DB_update_all("message","`remind_status`='1'","`fa_uid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("message2",'2');
		$this->yunset("js_def",7);
		$this->com_tpl('seemessage');
	}
	function sysnews_action(){
		if ($_POST['del']||$_GET['del']){
			if(is_array($_POST['del'])){
				$ids=$this->pylode(',',$_POST['del']);
				$layer_type='1';
			}else if($_GET['del']){
				$ids=(int)$_GET['del'];
				$layer_type='0';
			}
			$nid=$this->obj->DB_delete_all("sysmsg","`id` in(".$ids.") AND `fa_uid`='".$this->uid."'"," ");
			$nid?$this->layer_msg('删除成功！',9,$layer_type):$this->layer_msg('删除失败！',8,$layer_type);
		}
		$urlarr=array("c"=>"sysnews","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("sysmsg","`fa_uid`='".$this->uid."' order by id desc",$pageurl,"10");
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('sysnews');
	}
	function replys_action()
	{
		if($_POST['replys_message']){
			$user_message = $this->obj->DB_select_once("message","(`fa_uid`='".$this->uid."' or `uid`='".$this->uid."') AND `id`='".(int)$_POST['keyid']."'");
			if(empty($user_message)){
 				$this->obj->ACT_layer_msg("该信息不存在！",9,"index.php?c=seemessage");
			}
			$data['keyid']=(int)$_POST['keyid'];
			$data['content']=$_POST['content'];
			$data['fa_uid']=$this->uid;
			$data['username']=$this->username;
			$data['ctime']=mktime();
			$nid=$this->obj->insert_into("message",$data);
			$where['id']=(int)$_POST['keyid'];
			$where['fa_uid']=$this->uid;
			$this->obj->update_once("message",array("status"=>"1"),$where);
 			$nid?$this->obj->ACT_layer_msg("回复成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("添加失败！",8,$_SERVER['HTTP_REFERER']);
		}
		if($_GET['id']){
			$urlarr=array("c"=>"replys","id"=>$_GET['id'],"page"=>"{{page}}");
			$pageurl=$this->url("index","index",$urlarr);
			$this->get_page("message","`keyid`='".(int)$_GET['id']."' or `id`='".(int)$_GET['id']."' order by `id` desc",$pageurl,"10");
			$this->yunset("id",$_GET['id']);
			$this->public_action();
			$this->yunset("js_def",7);
			$this->com_tpl('replys');
		}else{
			$this->obj->ACT_layer_msg("该信息不存在！",9,"index.php?c=seemessage");
		}
	}
	function msg_action(){
		if($_POST['submit']){
			$data['reply']=$_POST['reply'];
			$data['reply_time']=time();
			$data['user_remind_status']='0';
			$where['id']=(int)$_POST['id'];
			$where['job_uid']=$this->uid;
			$id=$this->obj->update_once("msg",$data,$where);
 			isset($id)?$this->obj->ACT_layer_msg("回复成功！",9,"index.php?c=msg"):$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=msg");
		}
		if($_GET['del']){
			$where['id']=(int)$_GET['del'];
			$where['job_uid']=$this->uid;
			$nid=$this->obj->update_once("msg",array("del_status"=>"1"),$where);
			$this->unset_remind("commsg",'2');
			$nid?$this->layer_msg('删除成功！',9,0,"index.php?c=msg"):$this->layer_msg('删除失败！',8,0,"index.php?c=msg");
		}
		$urlarr=array("c"=>"msg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("msg","`job_uid`='".$this->uid."' and `del_status`<>'1' order by datetime desc",$pageurl,"5");
		if(is_array($rows)&&$rows){
			foreach($rows as $key=>$val){
				$rows[$key]['content']=strip_tags(trim($val['content']));
			}
		}
		$this->obj->DB_update_all("msg","`com_remind_status`='1'","`job_uid`='".$this->uid."' and `com_remind_status`='0'");
		$this->unset_remind("commsg",'2');
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('msg');
	}
	function pl_action()
	{
		if($_POST['submit'])
		{
			$data['reply']=$_POST['reply'];
			$data['reply_time']=time();
			$where['id']=(int)$_POST['id'];
			$where['cuid']=$this->uid;
			$nid=$this->obj->update_once("company_msg",$data,$where);
 			isset($nid)?$this->obj->ACT_layer_msg("回复成功！",9,"index.php?c=".$_GET['c']):$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=".$_GET['c']);
		}
		if($_GET['del'])
		{
			$info=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`pl_time`");
			if($info['pl_time']<time())
			{
				$this->layer_msg('请先购买企业评论管理功能！',8,0,"index.php?c=pl");
			}
			$del=(int)$_GET['del'];
			$nid=$this->obj->DB_delete_all("company_msg","`id`='".$del."' and `cuid`='".$this->uid."'");
			$nid?$this->layer_msg('删除成功！',9,0,"index.php?c=pl"):$this->layer_msg('删除失败！',8,0,"index.php?c=pl");
		}
		if($_GET['ajax']=="1" && $_POST['id'])
		{
			$where['uid']=$this->uid;
			$id=$this->obj->update_once("company",array("pl_status"=>(int)$_POST['id']),"`uid`='".$this->uid."' and `pl_time`>'".time()."'");
			if($id)
			{
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
		if($_POST['status'] && $_POST['id'])
		{
			$info=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`pl_time`");
			if($info['pl_time']>time())
			{
				$where['id']=(int)$_POST['id'];
				$where['cuid']=$this->uid;
				$nid=$this->obj->update_once("company_msg",array("status"=>(int)$_POST['status']),$where);
				if($nid)
				{
					echo 1;die;
				}else{
					echo 2;die;
				}
			}else{
				echo 0;die;
			}
		}
		$urlarr=array("c"=>"pl","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company_msg","`cuid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $k=>$v)
			{
				$uid[]=$v['uid'];
			}
			$uid=$this->pylode(",",$uid);
			$user=$this->obj->DB_select_all("resume","`uid` in ($uid)");
			foreach($rows as $k=>$v)
			{
				foreach($user as $val){
					if($v['uid']==$val['uid']){
						$rows[$k]['name']=$val['name'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$com=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		if($com['pl_time'] && $com['pl_time']>time())
		{
			$this->yunset("pl_time",'1');
		}
		$this->obj->update_once("company_msg",array("status"=>(int)$_POST['status']),array("id"=>(int)$_POST['id']));
		$this->yunset("com",$com);
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('pl');
	}
	function xin_action()
	{
		if($_GET['del'])
		{
			if($_GET['id'])
			{
				$nid = $this->obj->DB_delete_all("friend_message","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			}else{
				$where['id']=(int)$_GET['did'];
				$where['fid']=$this->uid;
				$nid = $this->obj->update_once("friend_message",array("status"=>"1"),$where);
			}
			$this->unset_remind("friend_message2",'2');
			$nid?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['submit'])
		{
			$data['content']=trim($_POST['content']);
			$data['ctime']=time();
			$data['pid']=$_POST['pid'];
			$data['fid']=$_POST['fid'];
			$data['nid']=$this->uid;
			$data['uid']=$this->uid;
			$nid=$this->obj->insert_into("friend_message",$data);
 			$nid?$this->obj->ACT_layer_msg("回复成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("添加失败！",8,$_SERVER['HTTP_REFERER']);
		}
		$where.= "`uid`='".$this->uid."' or (`fid`='".$this->uid."' and `status`<>'1') order by ctime desc";
		$urlarr['c'] = $_GET['c'];
		$urlarr["page"] = "{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("friend_message",$where,$pageurl,"12");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
				$uid[]=$v['fid'];
			}
			$statis =$this->obj->DB_select_all("friend_info","`uid` in (".@implode(",",$uid).")","`uid`,`nickname`");
			foreach($rows as $key=>$value)
			{
				foreach($statis as $k=>$v)
				{
					if($value['uid']==$v['uid'])
					{
						  $rows[$key]['nickname'] = $v['nickname'];
					}
					if($value['fid']==$v['uid'])
					{
						  $rows[$key]['name'] = $v['nickname'];
					}
				}
			}
		}
		$this->yunset("ownuid",$this->uid);
		$this->yunset("rows",$rows);
		$this->obj->DB_update_all("friend_message","`remind_status`='1'","`fid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("friend_message2",'2');
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('xin');
	}
	function reply_action()
	{
			$this->public_action();
			$this->yunset("js_def",7);
			$this->com_tpl('reply');
	}
	function vs_action(){
		$this->public_action();
		$this->yunset("js_def",6);
		$this->com_tpl('vs');
	}
	function passwd_action()
	{
		$this->public_action();
		if($_POST['submit'])
		{
			$info = $this->obj->DB_select_once("member","`uid`='".$this->uid."'");
			if(is_array($info))
			{
				$oldpass = md5(md5($_POST['oldpassword']).$info['salt']);
				if($info['password']!=$oldpass)
				{
 					$this->obj->ACT_layer_msg("原始密码错误！",8,"index.php?c=vs");
				}
				if($this->config['sy_uc_type']=="uc_center" && $info['name_repeat']!="1")
				{
					$this->obj->uc_open();
					$ucresult= uc_user_edit($info['username'], $_POST['oldpassword'], $_POST['password'], "","1");
					if($ucresult == -1) {
 						$this->obj->ACT_layer_msg("原始密码错误！",8,"index.php?c=vs");
					}
				}else{
					$salt = substr(uniqid(rand()), -6);
					$pass2 = md5(md5($_POST['password']).$salt);
					$data['password']=$pass2;
					$data['salt']=$salt;
					$this->obj->update_once("member",$data,array("uid"=>$this->uid));
				}
				$this->unset_cookie();
 				$this->obj->ACT_layer_msg("密码修改成功，请重新登录！",9,$this->config['sy_weburl']."/index.php?m=login&usertype=".$_POST['usertype']);
			}
		}
		$this->yunset("js_def",5);
		$this->user_tpl('passwd');
	}
	function hr_action()
	{
		if($_POST['ajax']==1 && $_POST['ids'])
		{
			$this->obj->DB_update_all("userid_job","`is_browse`='2'","`id` in (".$this->pylode(",",$_POST['ids']).") and `com_id`='".$this->uid."'");
			$this->unset_remind("userid_job",'2');
			die;
		}
		if($_POST['delid']||$_GET['delid']){
			if(is_array($_POST['delid'])){
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}else{
				$id=(int)$_GET['delid'];
				$layer_type='0';
			}
			$sq_num = $this->obj->DB_select_all("userid_job","`id` in (".$id.") and `com_id`='".$this->uid."'","`uid`");
			if(is_array($sq_num)){
				foreach($sq_num as $v){
					$a[]=$v['uid'];
		    	}
			}
			$user_id=$this->pylode(",",$a);
			$data['sq_jobnum']="`sq_jobnum`"-count($sq_num);
			$this->obj->update_once("member_statis",$data,"uid in (".$user_id.")");
			$nid=$this->obj->DB_delete_all("userid_job","`id` in (".$id.") and `com_id`='".$this->uid."'"," ");
			$this->unset_remind("userid_job",'2');
			$nid?$this->layer_msg('删除成功！',9,$layer_type,"index.php?c=hr"):$this->layer_msg('删除失败！',8,$layer_type,"index.php?c=hr");
		}
		if(!empty($_GET['keyword'])){
			$rows=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".$_GET['keyword']."%'","`uid`");
			if(is_array($rows) && !empty($rows)){
				foreach($rows as $v){
					$uidarr[]=$v['uid'];
				}
				$urlarr['keyword']=$_GET['keyword'];
				$where=" uid in (".$this->pylode(',',$uidarr).") and ";
			}
		}
		if((int)$_GET['job_id']){
			$where ="job_id=".(int)$_GET['job_id']." and ";
			$urlarr['job_id']=(int)$_GET['job_id'];
		}
		$this->public_action();
		$urlarr['c']="hr";
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("userid_job",$where."  `com_id`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows) && !empty($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
			}
			$userrows=$this->obj->DB_select_all("resume","`uid` in (".$this->pylode(",",$uid).") and `r_status`<>'2'","`name`,`sex`,`edu`,`uid`");
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
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('hr');
	}
	function blacklist_action()
	{
		if($_POST['delid']){
			if(is_array($_POST['delid'])){
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}else{
				$id=(int)$_POST['delid'];
				$layer_type='0';
			}
			$nid=$this->obj->DB_delete_all("blacklist","`id` in (".$id.") and `c_uid`='".$this->uid."'"," ");
			$nid?$this->layer_msg('删除成功！',9,$layer_type,"index.php?c=blacklist"):$this->layer_msg('删除失败！',8,$layer_type,"index.php?c=blacklist");
		}
		if(!empty($_GET['keyword'])){
			$rows=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".$_GET['keyword']."%'");
			if(is_array($rows) && !empty($rows))
			{
				foreach($rows as $v)
				{
					$uidarr[]=$v['uid'];
				}
				$urlarr['keyword']=$_GET['keyword'];
				$where="`p_uid` in (".$this->pylode(',',$uidarr).") and ";
			}
		}
		if($_GET['del_id']){
			$this->obj->DB_delete_all("blacklist","`id`='".(int)$_GET['del_id']."' and `c_uid`='".$this->uid."'");
 			$this->layer_msg('删除成功！',9,0,"index.php?c=blacklist");
		}
		$this->public_action();
		$urlarr["c"]="blacklist";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("blacklist",$where." `c_uid`='".$this->uid."' and `usertype`='2'",$pageurl,"10");

		if(is_array($rows) && !empty($rows)){
			foreach($rows as $v){
				$uid[]=$v['p_uid'];
			}
			$userrows=$this->obj->DB_select_all("resume","`uid` in (".$this->pylode(",",$uid).") and `r_status`<>'2'","`name`,`uid`,`sex`,`edu`");
			include(PLUS_PATH."user.cache.php");
			foreach($rows as $k=>$v){
				foreach($userrows as $val){
					if($v['p_uid']==$val['uid']){
						$rows[$k]['name']=$val['name'];
						$rows[$k]['sex']=$userclass_name[$val['sex']];
						$rows[$k]['edu']=$userclass_name[$val['edu']];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('blacklist');
	}
	function invite_action()
	{
		if($_POST['delid'] || $_GET['del'])
		{
			if($_GET['del'])
			{
				$id=(int)$_GET['del'];
				$layer_type='0';
			}else{
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}
			$nid=$this->obj->DB_delete_all("userid_msg","`id` in (".$id.") and `fid`='".$this->uid."'"," ");
			$nid?$this->layer_msg('删除成功！',9,$layer_type,"index.php?c=invite"):$this->layer_msg('删除失败！',8,$layer_type,"index.php?c=invite");
		}
		if(!empty($_GET['keyword']))
		{
			$rows=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".$_GET['keyword']."%'");
			if(is_array($rows))
			{
				foreach($rows as $v)
				{
					$uidarr[]=$v['uid'];
				}
			}
			$where="uid in (".$this->pylode(',',$uidarr).") and ";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$this->public_action();
		$urlarr['c']='invite';
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("userid_msg",$where." `fid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows) && !empty($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
			}
			$userrows=$this->obj->DB_select_all("resume","`uid` in (".$this->pylode(",",$uid).") and `r_status`<>'2'");
			if(is_array($userrows))
			{
				include(PLUS_PATH."user.cache.php");
				foreach($userrows as $va)
				{
					$user[$va['uid']]['name']=$va['name'];
					$user[$va['uid']]['sex']=$userclass_name[$va['sex']];
					$user[$va['uid']]['edu']=$userclass_name[$va['edu']];
				}
			}
			$this->yunset("user",$user);
		}
		$this->public_action();
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('invite');
	}
	function down_action()
	{
		if($_POST['delid'] || $_GET['del'])
		{
			if($_GET['del'])
			{
				$id=(int)$_GET['del'];
				$layer_type='0';
			}else{
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}
			$nid=$this->obj->DB_delete_all("down_resume","`id` in (".$id.") and `comid`='".$this->uid."'"," ");
			$nid?$this->layer_msg('删除成功！',9,$layer_type,"index.php?c=down"):$this->layer_msg('删除失败！',8,$layer_type,"index.php?c=down");
		}
		if(!empty($_GET['keyword']))
		{
			$rows=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".$_GET['keyword']."%'");
			if(is_array($rows))
			{
				foreach($rows as $v)
				{
					$uidarr[]=$v['uid'];
				}
			}
			$where="uid in (".$this->pylode(',',$uidarr).") and ";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$this->public_action();
		$urlarr['c']='down';
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("down_resume","$where `comid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows)){
			foreach($rows as $v){
				$uid[]=$v['uid'];
			}
			$userrows=$this->obj->DB_select_all("resume","`uid` in (".$this->pylode(",",$uid).") and `r_status`<>'2'","`name`,`uid`,`sex`,`edu`");

			if(is_array($userrows)){
				include(PLUS_PATH."user.cache.php");
				foreach($rows as $key=>$val){
					foreach($userrows as $va){
						if($val['uid']==$va['uid']){
							$rows[$key]['name']=$va['name'];
							$rows[$key]['sex']=$userclass_name[$va['sex']];
							$rows[$key]['edu']=$userclass_name[$va['edu']];
						}
					}
				}

			}
		}
		$this->yunset("rows",$rows);
		$report=$this->config['com_report'];
		$this->yunset("report",$report);
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('down');
	}
	function com_action()
	{
		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);
		$this->public_action();
		$statis = $this->company_satic();
		$urlarr=array("c"=>"com","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		if($statis['vip_etime']>time())
		{
			$statis['vip_time'] = date("Y年m月d日",$statis['vip_etime']);
		}else{
			$statis['vip_time'] = "已过期";
		}
		$this->yunset("statis",$statis);
		$rows = $this->get_page("company_order","uid='".$this->uid."' and `type`='1' order by id desc",$pageurl,"10");
		$this->yunset("rows",$rows);



		$pay1=$this->obj->DB_select_all("company_pay","`com_id`='".$this->uid."' and `type`='1' and `order_price`<0","SUM(order_price) as allprice");
		$pay2=$this->obj->DB_select_all("company_pay","`com_id`='".$this->uid."' and `type`='2' and `order_price`<0","SUM(order_price) as allprice");
		$this->yunset("price",str_replace("-","", $pay2[0]['allprice']));
		$this->yunset("integral",str_replace("-","", $pay1[0]['allprice']));
		$this->yunset("js_def",4);
		$this->com_tpl('com');
	}
	function payment_action(){
		if($_COOKIE['usertype']!='2' || $this->uid=='')
		{
			$this->obj->ACT_msg("index.php","非法操作！");
		}else{
			$c_order=$this->obj->DB_select_once("company_order","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' and `order_state`='1'");
			if(empty($c_order)){
 				$this->obj->ACT_msg("index.php","非法操作！");
			}else{
				$statis=$this->company_satic();
				$statis['pay_format']=number_format($statis['pay'],2);
				$order_remark="汇款银行：\n汇款卡号：\n汇款金额：\n汇款时间：\n其他：\n";
				$this->yunset("order",$c_order);
				$this->yunset("order_remark",$order_remark);
				$this->yunset("statis",$statis);
				$this->yunset("js_def",4);
				$this->public_action();
				$this->com_tpl('payment');
			}
		}
	}
	function balance_action(){
		$order=$this->obj->DB_select_once("company_order","`order_id`='".(int)$_POST['aliorder']."'");
		if($order['id']){
			$statis=$this->company_satic();
			if($statis['pay']>=$order['order_price']){
				if($_POST['is_invoice']=='1'&&$this->config['sy_com_invoice']){
					$is_invoice=",`is_invoice`='".$_POST['is_invoice']."'";
					$this->add_invoice_record($_POST,$order['order_id']);
				}
				$this->obj->DB_update_all("company_statis","`pay`=`pay`-'".$order['order_price']."'","`uid`='".$this->uid."'");
				$this->obj->DB_update_all("company_order","`order_type`='balance',`order_state`='2'".$is_invoice,"`id`='".$order['id']."'");
				$id=$this->insert_company_pay($order['order_price'],2,$this->uid,"余额支付：".$order['order_id'],2,$order['type']);
				if($id){
					$this->upuser_statis($order);
					$this->obj->ACT_layer_msg("支付成功！",9,"index.php?c=paylog");
				}else{
					$this->obj->ACT_layer_msg("支付失败，请重试！",8,$_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->obj->ACT_layer_msg("余额不足，请选择其他支付方式！",8,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}

	function paybank_action(){
		$order=$this->obj->DB_select_once("company_order","`order_id`='".(int)$_POST['aliorder']."'");
		if($order['id']){
			$company_order="`order_type`='bank',`order_state`='3',`order_remark`='".$_POST['order_remark']."'";
			if($_POST['is_invoice']=='1'&&$this->config['sy_com_invoice']=='1'){
				$company_order.=",`is_invoice`='".$_POST['is_invoice']."'";
				$this->add_invoice_record($_POST,$order['order_id']);
			}
			if($_POST['balance']){
				$statis=$this->company_satic();
				if($statis['pay']>=$order['order_price']){
					$this->obj->DB_update_all("company_statis","`pay`=`pay`-'".$order['order_price']."'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company_order","`order_price`='0',".$company_order,"`order_id`='".$order['order_id']."'");
					$this->upuser_statis($order);
				}else{
					$price=$statis['pay'];
					$this->obj->DB_update_all("company_statis","`pay`='0'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company_order","`order_price`=`order_price`-'".$price."',".$company_order,"`order_id`='".$order['order_id']."'");
				}
				$this->insert_company_pay($price,2,$this->uid,"余额支付：".$order['order_id'],2,$order['type']);
			}else{
				$this->obj->DB_update_all("company_order",$company_order,"`order_id`='".$order['order_id']."'");
			}
			$this->obj->ACT_layer_msg("操作成功，请等待管理员审核！",9,"index.php?c=paylog");
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function add_invoice_record($post,$order_id){
		if($post['linkway']=='1'){
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`linkman`,`linktel`,`address`");
			$link=",`link_man`='".$company['linkman']."',`link_moblie`='".$company['linktel']."',`address`='".$company['address']."'";
		}else{
			$post=$this->post_trim($post);
			if($post['link_man']==''||$post['link_moblie']==''||$post['address']==''){
				$this->obj->ACT_layer_msg("联系人、联系电话、寄送地址均不能为空！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$link=",`link_man`='".$post['link_man']."',`link_moblie`='".$post['link_moblie']."',`address`='".$post['address']."'";
			}
		}
		$record=$this->obj->DB_select_once("invoice_record","`order_id`='".$order_id."' and `uid`='".$this->uid."'","id");
		if($record['id']){
			$this->obj->DB_update_all("invoice_record","`title`='".trim($post['invoice_title'])."',`status`='0',`addtime`='".time()."'".$link,"`id`='".$record['id']."'");
		}else{
			$iid=$this->obj->DB_insert_once("invoice_record","`order_id`='".$order_id."',`uid`='".$this->uid."',`title`='".trim($post['invoice_title'])."',`status`='0',`addtime`='".time()."'".$link);
			if($iid==false||$iid==''){$this->obj->ACT_layer_msg("发票信息添加失败！",8,$_SERVER['HTTP_REFERER']);}
		}
	}
	function del_pay_action()
	{
		if($_COOKIE['usertype']!='2' || $this->uid=='')
		{
			$this->layer_msg('非法操作！',8,0,"index.php");
		}else{
			$oid=$this->obj->DB_select_once("company_order","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' and `order_state`='1'");
			if(empty($oid))
			{
				echo '0';die;
			}else{
				echo '1';die;
			}
		}
	}
	function dingdan_action(){
		if($_POST['price']){
			if($_POST['comvip']){
				$comvip=(int)$_POST['comvip'];
				$ratinginfo =  $this->obj->DB_select_once("company_rating","`id`='".$comvip."'");
				$price = $ratinginfo['service_price'];
				$data['type']='1';
			}elseif($_POST['price_int']){
				$price = $_POST['price_int']/$this->config['integral_proportion'];
				$data['type']='2';
			}elseif($_POST['price_msg']){
				$price = $_POST['price_msg']/$this->config['integral_msg_proportion'];
				$data['type']='5';
			}else{
 				$this->obj->ACT_layer_msg("参数不正确，请正确填写！",8,$_SERVER['HTTP_REFERER']);
			}
			$dingdan=mktime().rand(10000,99999);
			$data['order_id']=$dingdan;
			$data['order_price']=$price;
			$data['order_time']=mktime();
			$data['order_state']="1";
			$data['order_remark']=trim($_POST['remark']);
			$data['uid']=$this->uid;
			$data['rating']=$_POST['comvip'];
			$data['integral']=$_POST['price_int'];
			$id=$this->obj->insert_into("company_order",$data);
			if($id){
				$this->obj->ACT_layer_msg("下单成功，请付款！",9,"index.php?c=payment&id=".$id);
			}else{
				$this->obj->ACT_layer_msg("提交失败，请重新提交订单！",8,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->obj->ACT_layer_msg("参数不正确，请正确填写！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function info_action()
	{
		$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$this->yunset("row",$row);
		if($_POST['submitbtn'])
		{
			$_POST=$this->post_trim($_POST);
			if(trim($_POST['linktel'])){
				$is_exist=$this->obj->DB_select_once("member","`uid`<>'".$this->uid."' and (`email`='".$_POST['linkmail']."' or `moblie`='".$_POST['linktel']."')","`uid`");
			}else{
				$is_exist=$this->obj->DB_select_once("member","`uid`<>'".$this->uid."' and `email`='".$_POST['linkmail']."'","`uid`");
			}
			if($is_exist['uid']){
				$this->obj->ACT_layer_msg("手机或邮箱已存在！",2,$_SERVER['HTTP_REFERER']);
			}
			if($_POST['name']=="")
			{
				$this->obj->ACT_layer_msg("企业全称不能为空！",8,"index.php?c=info");
			}
			if($_POST['hy']=="")
			{
				$this->obj->ACT_layer_msg("从事行业不能为空！",8,"index.php?c=info");
			}
			if($_POST['pr']=="")
			{
				$this->obj->ACT_layer_msg("企业性质不能为空！",8,"index.php?c=info");
			}
			if($_POST['provinceid']=="")
			{
				$this->obj->ACT_layer_msg("企业地址不能为空！",8,"index.php?c=info");
			}
			if($_POST['mun']=="")
			{
				$this->obj->ACT_layer_msg("企业规模不能为空！",8,"index.php?c=info");
			}
			if($_POST['address']=="")
			{
				$this->obj->ACT_layer_msg("公司地址不能为空！",8,"index.php?c=info");
			}
			if($_POST['linkphone']=="")
			{
				$this->obj->ACT_layer_msg("固定电话不能为空！",8,"index.php?c=info");
			}
			if($_POST['linkmail']=="")
			{
				$this->obj->ACT_layer_msg("联系邮件不能为空！",8,"index.php?c=info");
			}
			if($_POST['content']=="")
			{
				$this->obj->ACT_layer_msg("企业简介不能为空！",8,"index.php?c=info");
			}
			$this->obj->delfiledir("../upload/tel/".$this->uid);
			unset($_POST['submitbtn']);
				if($_FILES['uplocadpic']['tmp_name'])
				{
					$upload=$this->upload_pic("../upload/company/",false,$this->config['com_pickb']);
					$pictures=$upload->picture($_FILES['uplocadpic']);
					$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
					$s_thumb=$upload->makeThumb($pictures,150,80,'_S_');
					$this->obj->unlink_pic($pictures);
					$_POST['logo']=str_replace("../upload/company","./upload/company",$s_thumb);
					$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."' and `logo`<>''");
					if(is_array($row))
					{
						$this->obj->unlink_pic(".".$row['logo']);
					}
				}
				if($_FILES['firmpic']['tmp_name'])
				{
					$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
					$firmpic=$upload->picture($_FILES['firmpic']);
					$this->picmsg($firmpic,$_SERVER['HTTP_REFERER']);
					$_POST['firmpic'] = str_replace("../upload/company","./upload/company",$firmpic);
					$rows=$this->obj->DB_select_once("company","`uid`='".$this->uid."' and `firmpic`<>''");
					if(is_array($rows))
					{
						$this->obj->unlink_pic(".".$rows['firmpic']);
					}
				}
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
			$_POST['content'] = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'background-color:','background-color:','white-space:'),html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$_POST['lastupdate']=mktime();
			$nid=$this->obj->update_once("company",$_POST,$where);
			$data['com_name']=$_POST['name'];
			$data['pr']=$_POST['pr'];
			$data['mun']=$_POST['mun'];
			$data['com_provinceid']=$_POST['provinceid'];
			$this->obj->update_once("company_job",$data,array("uid"=>$this->uid));
			$this->obj->update_once("member",array("email"=>$_POST['linkmail'],"moblie"=>$_POST['linktel']),array("uid"=>$this->uid));
			$this->obj->update_once("userid_job",array("com_name"=>$_POST['name']),array("com_id"=>$this->uid));
			$this->obj->update_once("fav_job",array("com_name"=>$_POST['name']),array("com_id"=>$this->uid));
			$this->obj->update_once("report",array("r_name"=>$_POST['name']),array("c_uid"=>$this->uid));
			$this->obj->update_once("blacklist",array("com_name"=>$_POST['name']),array("c_uid"=>$this->uid));
			$this->obj->update_once("msg",array("com_name"=>$_POST['name']),array("job_uid"=>$this->uid));
			$nid?$this->obj->ACT_layer_msg("更新成功！",9,"index.php?c=info"):$this->obj->ACT_layer_msg("更新失败！",8,"index.php?c=info");
		}
		$this->public_action();
		$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$this->yunset("row",$row);
		$this->city_cache();
		$this->job_cache();
		$this->com_cache();
		$this->industry_cache();
		$this->yunset("js_def",2);
		$this->com_tpl('info');
	}
	function right_action()
	{
		$this->public_action();
		$member[]=array("有效时间","service_time","天","不限");
		$member[]=array("<font color=red>服务价格</font>","service_price","元","0元");
		$member[]=array("发布职位数","job_num","份","不限");
		$member[]=array("下载简历数","resume","份","不限");
		$member[]=array("邀请人才面试数","interview","份","不限");
		$member[]=array("修改职位数","editjob_num","次","不限");
		$member[]=array("刷新职位数","breakjob_num","次","不限");
		$member[]=array("说明","explains","","无");
		$member[]=array("<font color=red>".$this->config['integral_pricename']."购买</font>","integral_buy",$this->config['integral_priceunit'],"0个");
		$this->yunset("member",$member);
		if($this->config['com_vip_type']==1)
		{
			$where = " and `type`='2'";
		}elseif($this->config['com_vip_type']==2){
			$where = " and `type`='1'";
		}
		$rows=$this->obj->DB_select_all("company_rating","`display`='1' and `id`<>'".$this->config['com_rating']."' and `category`=1 ".$where." order by `sort` asc");
		$num=count($rows);
		if($num>8)
		{
			foreach($rows as $k=>$v)
			{
				if($k<$num/2){
					$rows1[]=$v;
				}else{
					$rows2[]=$v;
				}
			}
			$rows=$rows1;
		}
		$statis=$this->company_satic();
		$this->yunset("statis",$statis);
		$this->yunset("rows",$rows);
		$this->yunset("rows2",$rows2);
		$this->yunset("js_def",4);
		$this->com_tpl('member_right');
	}
	function buyvip_action()
	{
		$this->public_action();
		$this->company_satic();
		$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_GET['vipid']."' and display='1'");
		$this->yunset("row",$row);
		$this->yunset("js_def",4);
		if($_GET['vipid']==0)
		{
			$this->com_tpl('buypl');
		}else{
			$this->com_tpl('buyvip');
		}
	}
	function buysave_action(){
		$statis=$this->company_satic();
		if($_POST['type']=='vip'){
			$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_POST['vipid']."'");
			$price=$row[service_price];
			$integral=$row[integral_buy];
		}elseif($_POST['type']=='ad'){
			$row=$this->obj->DB_select_once("ad_class","`id`='".(int)$_POST['aid']."' and `type`='1'");
			if($row['id']){
				$price=$row['price_buy']*$_POST['buy_time'];
				$integral=$row['integral_buy']*$_POST['buy_time'];
			}else{
				$this->obj->ACT_msg("index.php?c=ad","非法操作！");
			}
		}else{
			$integral=$this->config['integral_com_comments']*$_POST['time'];
		}
		if($_POST['buytype']==2){
			if($statis['integral']<$integral){
 				$this->obj->ACT_layer_msg("你的".$this->config['integral_pricename']."不足，请先充值！",8,"index.php?c=pay");
			}
		}else{
			if($statis['pay']<$price){
				$this->obj->ACT_layer_msg("你的余额不足，请先充值！",8,"index.php?c=pay");
			}
		}

		if($_POST['type']=='pl'){
			$this->obj->company_invtal($this->uid,$integral,false,"购买企业评论管理");
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`pl_time`");
			if($company['pl_time']>time()){
				$pl_time=$company['pl_time']+86400*30*$_POST['time'];
			}else{
				$pl_time=time()+86400*30*$_POST['time'];
			}
			$oid=$this->obj->update_once("company",array("pl_time"=>$pl_time),array("uid"=>$this->uid));
			if($oid){
				$this->obj->ACT_layer_msg("您已购买成功！",9,"index.php");
			}else{
 				$this->obj->ACT_layer_msg("购买失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
			}
		}

		if($_POST['type']=='vip'){
			if($integral<0||$price<0){
				$this->obj->ACT_layer_msg("数量错误！",8,$_SERVER['HTTP_REFERER']);
			}else{
				if($_POST['buytype']==2){
					$nid=$this->obj->company_invtal($this->uid,$integral,false,"购买".$row['name']);
				}else{
					$nid=$this->obj->company_invtal($this->uid,$price,false,"购买".$row['name'],true,2,"pay");
				}
				if($nid){
					$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_POST['vipid']."'");
					$value="`rating`='".(int)$_POST['vipid']."',";
					$value.="`rating_name`='".$row['name']."',";
					if($row['type']=="2"){
						if($statis['vip_etime']==0){
							$viptime=mktime()+$row['service_time']*86400;
						}else{
							$viptime=$row['service_time']*86400;
						}
						$value.="`vip_etime`=`vip_etime`+'".$viptime."'";
					}else{
						$value.="`job_num`=`job_num`+".$row['job_num'].",";
						$value.="`down_resume`=`down_resume`+".$row['resume'].",";
						$value.="`invite_resume`=`invite_resume`+".$row['interview'].",";
						$value.="`editjob_num`=`editjob_num`+".$row['editjob_num'].",";
						$value.="`breakjob_num`=`breakjob_num`+".$row['breakjob_num'];
					}
					$oid=$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company_job","`rating`='".(int)$_POST['vipid']."'","`uid`='".$this->uid."'");
					if($oid){
						$this->obj->ACT_layer_msg("您已购买成功！",9,"index.php");
					}else{
						$this->obj->ACT_layer_msg("购买失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->obj->ACT_layer_msg("系统出错，请联系管理员！",8,"index.php");
				}
			}
		}
	}
	function cert_action()
	{
		$this->get_user();
		$rows=$this->obj->DB_select_all("company_cert","`uid`='".$this->uid."' group by type order by id desc");
		foreach($rows as $v){
			$row[$v['type']]=$v;
		}
		$this->yunset("email_row",$row['1']);
		$this->yunset("moblie_row",$row['2']);
		$this->yunset("yyzz_row",$row['3']);
		$this->yunset("type",$_GET['type']);
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl('cert');
	}
	function uploadcert_action()
	{
		$upload=$this->upload_pic("../upload/cert/",false,$this->config['com_uppic']);
		$pictures=$upload->picture($_FILES['fileToUpload']);

		$this->picmsg($pictures,$_SERVER['HTTP_REFERER'],"ajax");
			if($this->config['com_cert_status']=="1")
			{
				$sql['status']=0;
			}else{
				$sql['status']=1;
				$this->obj->DB_update_all("company","`cert` = concat(`cert`,',3')","`uid`='".$this->uid."'");

				$this->obj->update_once("friend_info",array("iscert"=>$sql['status']),array("uid"=>$this->uid));
			}
			$sql['step']=1;
			$sql['check']=str_replace("../","/",$pictures);
			$sql['check2']="0";
			$sql['ctime']=mktime();
			$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='3'");
			if(is_array($row))
			{
				$where['uid']=$this->uid;
				$where['type']='3';
				$this->obj->update_once("company_cert",$sql,$where);
			}else{
				$sql['uid']=$this->uid;
				$sql['type']=3;
				$this->obj->insert_into("company_cert",$sql);
			}
			if($this->config['com_cert_status']!="1")
			{
				$pictures=0;
			}
		echo "{";
		echo				"url: '".$pictures."',\n";
		echo "}";
	}
	function Resetrz_action()
	{
		if($_POST['type'])
		{
			if($_POST['type']=="3")
			{
				$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='3'");
				if(is_array($row))
				{
					$this->obj->unlink_pic("../".$row['check']);
				}
			}
			$this->obj->DB_delete_all("company_cert","`uid`='".$this->uid."' AND `type`='".(int)$_POST['type']."'");
			echo "1";
		}
	}
	function config_action()
	{
		$this->public_action();
		$this->yunset("js_def",6);
		$this->com_tpl('config');
	}
   function comtpl_action()
   {
		$statis=$this->company_satic();
		$this->yunset("buytpl",@explode(",",$statis['comtpl_all']));
		$list=$this->obj->DB_select_all("company_tpl","`status`='1' order by id desc");
		$this->yunset("list",$list);
		if($_POST['savetpl'])
		{
			foreach($list as $v)
			{
				$tplid[]=$v['id'];
			}
			if(in_array($_POST['tpl'],$tplid))
			{
				$row=$this->obj->DB_select_once("company_tpl","`id`='".(int)$_POST['tpl']."'");
				if(strstr($statis['comtpl_all'],$row['url'])==false)
				{
					if($row['type']==1)
					{
						if($statis['integral']<$row['price'])
						{
							$this->obj->ACT_layer_msg("您的".$this->config['integral_pricename']."不足，请先充值！",8,"index.php?c=pay");
						}
						$content="够买了企业模板 <a href=\"".$this->config['sy_weburl']."/company/index.php?id=".$this->uid."\">".$_POST[tplname.$_POST['tpl']]."</a>";
						$this->addstate($content);
						$nid=$this->obj->company_invtal($this->uid,$row['price'],false,"购买企业模板");
					}else{
						if($statis['pay']<$row['price'])
						{
							$this->obj->ACT_layer_msg("您的余额不够购买，请先充值！",8,"index.php?c=pay");
						}
						$content="够买了企业模板 <a href=\"".$this->config['sy_weburl']."/company/index.php?id=".$this->uid."\">".$_POST[tplname.$_POST['tpl']]."</a>";
						$this->addstate($content);
						$nid=$this->obj->company_invtal($this->uid,$row['price'],false,"购买企业模板",true,2,"pay");
					}
					if($statis['comtpl_all']=='')
					{
						$this->obj->update_once("company_statis",array("comtpl_all"=>$row['url']),array("uid"=>$this->uid));
					}else{
						$this->obj->DB_update_all("company_statis","`comtpl_all`=concat(`comtpl_all`,',$row[url]')","`uid`='".$this->uid."'");
					}
				}
				$oid=$this->obj->update_once("company_statis",array("comtpl"=>$row['url']),array("uid"=>$this->uid));
 				$oid?$this->obj->ACT_layer_msg("设置成功！",9,"index.php?c=comtpl"):$this->obj->ACT_layer_msg("设置失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
			}else{
 				$this->obj->ACT_layer_msg("请正确选择模版！",8,"index.php?c=comtpl");
			}
		}
		$this->public_action();
		$this->yunset("js_def",6);
		$this->com_tpl('comtpl');
	}
	function delask_action()
	{
		$del=(int)$_GET['id'];
		$is_del=$this->obj->DB_delete_all("question","`id`='".$del."' and uid='".$this->uid."'");
		if(!empty($is_del))
		{
			$this->obj->DB_delete_all("answer","`qid`='".$del."'","");
			$d_a=$this->obj->DB_delete_all("answer_review","`qid`='".$del."'","");
		}
		$d_a?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
	}
	function map_action()
	{
		if($_POST['savemap'])
		{
			$data['x']=(float)$_POST['xvalue'];
			$data['y']=(float)$_POST['yvalue'];
			$oid=$this->obj->update_once("company",$data,array("uid"=>$this->uid));
 			$oid?$this->obj->ACT_layer_msg("地图设置成功！",9,"index.php?c=map"):$this->obj->ACT_layer_msg("地图设置失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
		}
		$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","x,y,address,provinceid,cityid");
		$this->yunset("row",$row);
		$this->public_action();
		$this->city_cache();
		$this->yunset("js_def",2);
		$this->com_tpl('map');
	}
	function show_action()
	{
		if($_GET['did'])
		{
			$row=$this->obj->DB_select_once("company_show","`id`='".(int)$_GET['did']."' and `uid`='".$this->uid."'","`picurl`");
			if(is_array($row))
			{
				$this->obj->unlink_pic(".".$row['picurl']);
				$oid=$this->obj->DB_delete_all("company_show","`id`='".(int)$_GET['did']."' and `uid`='".$this->uid."'");
			}
			$oid?$this->layer_msg('删除成功！',9):$this->layer_msg('删除失败！',8);
		}
		$urlarr['c']="show";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("company_show","`uid`='".$this->uid."' order by id desc",$pageurl,"12","`title`,`id`,`picurl`");
		$sessionid=session_id();
		$this->yunset("sessionid",$sessionid);
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl('show');
	}
	function addshow_action()
	{
		$this->public_action();
		$this->yunset("uid",$this->uid);
		$this->yunset("js_def",2);
		$this->com_tpl('addshow');
	}
	function delshow_action(){
		$ids=$_POST['ids'];
		$company_show=$this->obj->DB_select_all("company_show","`id` in (".$ids.") and `uid`='".$this->uid."'","`picurl`");
		if(is_array($company_show)&&$company_show){
			foreach($company_show as $val){
				$this->obj->unlink_pic(".".$val['picurl']);
			}
			$this->obj->DB_delete_all("company_show","`id` in (".$ids.") and `uid`='".$this->uid."'","");
		}
		return true;
	}
	function saveshow_action(){
		if($_POST['submitbtn']){
			$pid=$this->pylode(',',$_POST['id']);
			$company_show=$this->obj->DB_select_all("company_show","`id` in (".$pid.") and `uid`='".$this->uid."'","`id`");
			if($company_show&&is_array($company_show)){
				foreach($company_show as $val){
					$title=$_POST['title_'.$val['id']];
					$this->obj->update_once("company_show",array("title"=>trim($title)),array("id"=>(int)$val['id']));
				}
				$this->obj->ACT_layer_msg("保存成功！",9,"index.php?c=show");
			}else{
				$this->obj->ACT_layer_msg("非法操作！",3,"index.php");
			}
		}else{
			$this->obj->ACT_msg("index.php","非法操作！");
		}
	}
	function news_action(){
		$this->public_action();
		$where='';
		if($_POST['delid'] || $_GET['delid'])
		{
	    	if($_POST['delid'] || $_GET['delid'])
	    	{
	    		if(is_array($_POST['delid']))
	    		{
	    			$delid=$this->pylode(",",$_POST['delid']);
					$layer_type='1';
		    	}else{
		    		$delid=(int)$_GET['delid'];
					$layer_type='0';
		    	}
				$oid=$this->obj->DB_delete_all("company_news","`id` in (".$delid.") and `uid`='".$this->uid."'","");
				$oid?$this->layer_msg('删除成功！',9,$layer_type):$this->layer_msg('删除失败！',8,$layer_type);
	    	}else{
 	    		$this->obj->ACT_layer_msg("请选择您要删除的新闻！",8,$_SERVER['HTTP_REFERER']);
	    	}
	    }
		if($_POST['action']=="save")
		{
			$sql['title']=$_POST['title'];
			$body = str_replace("&amp;","&",html_entity_decode($_POST['body'],ENT_QUOTES,"GB2312"));
			$title=trim($sql['title']);
			if($title=="" || $body=="")
			{
 				$this->obj->ACT_layer_msg("新闻标题内容不能为空！",2,$_SERVER['HTTP_REFERER']);
			}
			$sql['body']=$body;
			if(!$_POST['id'])
			{
				$sql['uid']=$this->uid;
				$sql['ctime']=mktime();
				$oid=$this->obj->insert_into("company_news",$sql);
			}else{
				$where['uid']=$this->uid;
				$where['id']=(int)$_POST['id'];
				$sql['status']='0';
				$oid=$this->obj->update_once("company_news",$sql,$where);
			}
 			$oid?$this->obj->ACT_layer_msg("处理成功！",9,"index.php?c=news"):$this->obj->ACT_layer_msg("处理失败，请稍后再试！",8,"index.php?c=news");
		}
		$where="`uid`='".$this->uid."'";
		if($_GET['action']=="add")
		{
			$tp="addnews";
		}else{
			$tp="news";
			if(trim($_GET['keyword']))
			{
				$urlarr['keyword']=$_GET['keyword'];
				$where.=" and `title` like '%".trim($_GET['keyword'])."%'";
			}
			$urlarr['c']="news";
			$urlarr["page"]="{{page}}";
			$pageurl=$this->url("index","index",$urlarr);
			$this->get_page("company_news",$where,$pageurl,"10","`title`,`id`,`status`,`ctime`,`statusbody`");
		}
		if($_GET['editid'])
		{
			$tp="addnews";
			$editrow=$this->obj->DB_select_once("company_news","`id`='".(int)$_GET['editid']."'");
			$this->yunset("editrow",$editrow);
		}
		$this->yunset("js_def",2);
		$this->com_tpl($tp);
	}
	function product_action()
	{
		$this->public_action();
		if($_GET['delid']){
			if(is_array($_GET['delid'])){
				$ids=$this->pylode(',',$_GET['delid']);
				$layer_type=1;
			}else{
				$ids=(int)$_GET['delid'];
				$layer_type=0;
			}
			$row=$this->obj->DB_select_all("company_product","`id` in (".$ids.") and `uid`='".$this->uid."'","`pic`");
			if(is_array($row)){
				foreach($row as $k=>$v){
					$this->obj->unlink_pic("..".$v['pic']);
				}
			}
			$oid=$this->obj->DB_delete_all("company_product","`id` in (".$ids.") and `uid`='".$this->uid."'","");
			$oid?$this->layer_msg('删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['submit']){
			$sql['title']=$_POST['title'];
			$body = str_replace("&amp;","&",html_entity_decode($_POST['body'],ENT_QUOTES,"GB2312"));
			$sql['body']=$body;
			if($_FILES['pic']['tmp_name'])
			{
				$upload=$this->upload_pic("../upload/product/",false,$this->config['com_uppic']);
				$pictures=$upload->picture($_FILES['pic']);
				$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
				$sql['pic']=str_replace("../","/",$pictures);
			}
			if(!$_POST['id']){
				$sql['uid']=$this->uid;
				$sql['ctime']=mktime();
				$oid=$this->obj->insert_into("company_product",$sql);
			}else{
				$where['uid']=$this->uid;
				$where['id']=(int)$_POST['id'];
				$sql['status']=0;
				if($_FILES['pic']['tmp_name']){
					$pictures=$upload->picture($_FILES['pic']);
					$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
					$sql['pic']=str_replace("../","/",$pictures);
					$row=$this->obj->DB_select_once("company_product","`id`='".(int)$_POST['id']."' and `uid`='".$this->uid."'","pic");
					if(is_array($row)){
						$this->obj->unlink_pic("..".$row['pic']);
					}
				}

				$oid=$this->obj->update_once("company_product",$sql,$where);
			}
 			$oid?$this->obj->ACT_layer_msg("操作成功！",9,"index.php?c=product"):$this->obj->ACT_layer_msg("操作失败，请稍后再试！",8,"index.php?c=product");
		}
		$where="`uid`='".$this->uid."'";
		if($_GET['action']=="add"){
			$tp="addproduct";
		}else{
			$tp="product";
			if(!empty($_GET['keyword']))
			{
				$urlarr['keyword']=$_GET['keyword'];
				$where.=" and `title` like '%".$_GET['keyword']."%'";
			}
			$urlarr['c']="product";
			$urlarr["page"]="{{page}}";
			$pageurl=$this->url("index","index",$urlarr);
			$this->get_page("company_product",$where,$pageurl,"10","`title`,`id`,`status`,`ctime`,`statusbody`");
		}
		if($_GET['editid'])
		{
			$tp="addproduct";
			$editrow=$this->obj->DB_select_once("company_product","`id`='".(int)$_GET['editid']."'");
			$this->yunset("editrow",$editrow);
		}
		$this->yunset("js_def",2);
		$this->com_tpl($tp);
	}
	function zhaopinhui_action(){
		if($_GET['del']){
			$delid=$this->obj->DB_delete_all("zhaopinhui_com","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'"," ");
			$delid?$this->layer_msg('退出成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('退出失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
		$this->public_action();
		$urlarr["c"]="zhaopinhui";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$where="`uid`='".$this->uid."'";
		$rows=$this->get_page("zhaopinhui_com",$where,$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $key=>$v)
			{
				$zphid[]=$v['zid'];
			}
			$zphrows=$this->obj->DB_select_all("zhaopinhui","`id` in (".$this->pylode(',',$zphid).")");

			foreach($rows as $k=>$v)
			{
				foreach($zphrows as $val)
				{
					if($v['zid']==$val['id'])
					{
						$rows[$k]['title']=$val['title'];
						$rows[$k]['address']=$val['address'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->yunset("js_def",2);
		$this->com_tpl("zhaopinhui");
	}
	function banner_action()
	{
		if($_POST['submit'])
		{
			$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
			$pic=$upload->picture($_FILES['pic']);
			$this->picmsg($pic,$_SERVER['HTTP_REFERER']);
			$data['uid']=$this->uid;
			$data['pic']=$pic;
			$this->obj->insert_into("banner",$data);
 			$this->obj->ACT_layer_msg("设置成功！",9,"index.php?c=banner");
		}
		if($_POST['update'])
		{
			$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
			$pic=$upload->picture($_FILES['pic']);
			$this->picmsg($pic,$_SERVER['HTTP_REFERER']);
			$row=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
			if(is_array($row))
			{
				$this->obj->unlink_pic($row['pic']);
			}
			$this->obj->update_once("banner",array("pic"=>$pic),array("uid"=>$this->uid));
 			$this->obj->ACT_layer_msg("设置成功！",9,"index.php?c=banner");
		}
		$banner=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
		$this->yunset("banner",$banner);
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl("banner");
	}
 function resume_list_action()
 {
	if($_GET['jobid'])
	{
		$resume=$this->obj->DB_select_all("resume","`r_status`<>'2'");
		foreach($resume as $k=>$v)
		{
			$def_job[]=$v['def_job'];
		}
		$urlarr['c']="resume_list";
		$urlarr['jobid']=$_GET['jobid'];
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("resume_expect","`job_post`='".(int)$_GET['jobid']."' and `id` in (".$this->pylode(",",$def_job).")",$pageurl,"10");
		include APP_PATH."/plus/user.cache.php";
		include APP_PATH."/plus/city.cache.php";
		if(is_array($rows))
		{
			foreach($rows as $k=>$v)
			{
				$uid[]=$v['uid'];
				$rows[$k]['salary_info']=$userclass_name[$v['salary']];
				$rows[$k]['province']=$city_name[$v['provinceid']];
				$rows[$k]['city']=$city_name[$v['cityid']];
				foreach($resume as $key=>$val)
				{
					if($v['uid']==$val['uid'])
					{
						$rows[$k]['name_info']=$val['name'];
						$rows[$k]['edu_info']=$userclass_name[$val['edu']];
						$rows[$k]['exp_info']=$userclass_name[$val['exp']];
					}
				}
			}
		}
		$look=$this->obj->DB_select_all("look_resume","`uid` in (".$this->pylode(",",$uid).")");
		if(is_array($look))
		{
			foreach($look as $v)
			{
				$looks[$v['uid']]++;
			}
		}
		$this->yunset("looks",$looks);
		$this->yunset("rows",$rows);
		$this->public_action();
		$this->com_tpl('resume_list');
	}
 }
	function uploadall_action()
	{
		if (isset($_POST['phpsessionid']))
		{
				session_id($_POST['phpsessionid']);
			} else if (isset($_GET['phpsessionid'])){
				session_id($_GET['phpsessionid']);
			}
			session_start();
			$POST_MAX_SIZE = ini_get('post_max_size');
			$unit = strtoupper(substr($POST_MAX_SIZE, -1));
			$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
			if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE)
			{
				header("HTTP/1.1 500 Internal Server Error");
				echo "fai:超过最大允许后的尺寸";
				exit(0);
			}
			$filenameset=false;
			$upbool=1;
			$tipmsg="";
			$dir_file=date("Ymd");
			$qhjsw=date('YmdHis');
			$imgpath="../upload/show/";
			$rootfoldername="null";
			$save_path = getcwd() .'/'.$imgpath.$dir_file.'/';
			$upload_name = "Filedata";
			$max_file_size_in_bytes = 2147483647;
			$extension_whitelist = array("jpg","jpeg","gif","png");
			$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';
			$MAX_FILENAME_LENGTH = 260;
			$file_name = "";
			$file_extension = "";
			$uploadErrors = array(
		        0=>"没有错误,文件上传有成效",
		        1=>"上传的文件的upload_max_filesize指令在你只有超过",
		        2=>"上传的文件的超过MAX_FILE_SIZE指示那个没有被指定在HTML表单",
		        3=>"未竟的上传的文件上传",
		        4=>"没有文件被上传",
		        6=>"错过一个临时文件夹"
			);
			if($upbool===0)
			{
				$this->HandleError("fai:".$tipmsg);
				exit(0);
			}
			if (!isset($_FILES['$upload_name']))
			{
				$this->HandleError("fai:没有发现上传 \$_FILES for " . $upload_name);
				exit(0);
			} else if (isset($_FILES['$upload_name']['error']) && $_FILES['$upload_name']['error'] != 0)
			{
				$this->HandleError($uploadErrors[$_FILES['$upload_name']['error']]);
				exit(0);
			} else if (!isset($_FILES['$upload_name']['tmp_name']) || !@is_uploaded_file($_FILES['$upload_name']['tmp_name']))
			{
				$this->HandleError("fai:Upload failed is_uploaded_file test.");
				exit(0);
			} else if (!isset($_FILES['$upload_name']['name']))
			{
				$this->HandleError("fai:文件没有名字.");
				exit(0);
			}
			list($width,$height,$type,$attr) = getimagesize($_FILES['$upload_name']['tmp_name']);
		 	if(empty($width) || empty($height) || empty($type) || empty($attr))
			{
		  		$this->HandleError("fai:上传图片为非法内容");
		  		exit(0);
		  	}
			$file_size = @filesize($_FILES['$upload_name']['tmp_name']);
			if ($file_size > $max_file_size_in_bytes)
			{
				$this->HandleError("fai:超过最高允许的文件的大小");
				exit(0);
			}
			if ($file_size <= 0)
			{
				$this->HandleError("fai:超出文件的最小大小");
				exit(0);
			}
			$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES['$upload_name']['name']));
			if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH)
			{
				$this->HandleError("fai:无效的文件");
				exit(0);
			}
			if(!$this->create_folders($save_path))
			{
				$this->HandleError("fai:文件夹创建失败");
				exit(0);
			}
			if (file_exists($save_path . $file_name))
			{
				$this->HandleError("fai:这个名字的文件已经存在");
				exit(0);
			}
			$path_info = pathinfo($_FILES['$upload_name']['name']);
			$file_extension = $path_info['extension'];
			$is_valid_extension = false;
			foreach ($extension_whitelist as $extension)
			{
				if (strcasecmp($file_extension, $extension) == 0)
				{
					$is_valid_extension = true;
					break;
				}
			}
			if (!$is_valid_extension)
			{
				$this->HandleError("fai:无效的扩展名");
				exit(0);
			}
			if (file_exists($save_path . $file_name))
			{
				$this->HandleError("fai:这个文件的名称已经存在");
				exit(0);
			}
			if(is_dir($imgpath.$dir_file))
			{
				$fileName=$filenameset?$this->createdatefilename($file_extension):$this->CreateNextName($file_extension,$save_path);
				if(!move_uploaded_file($_FILES['$upload_name']['tmp_name'], $save_path.$fileName))
				{
					$this->HandleError("fai:文件移动失败");
					exit(0);
			 	}else{
			 	     if($rootfoldername!=="null")
			         {
				       $this->HandleError("suc".$rootfoldername."/".$imgpath.$dir_file."/,".$fileName.",".$file_size);
			         }
			       else
			          {
				       $this->HandleError("suc".$imgpath.$dir_file."/,".$fileName.",".$file_size);
			          }
			 			exit(0);
			 		}
			}else{
				if(mkdir($imgpath.$dir_file))
				{
					$fileName=$filenameset?$this->createdatefilename($file_extension):$this->CreateFirstName($file_extension);
					if(!move_uploaded_file($_FILES['$upload_name']['tmp_name'], $save_path.$fileName))
					{
						$this->HandleError("fai:文件移动失败");
						exit(0);
			 		}else{
				 		if($rootfoldername!=="null")
				 		{
				 			$this->HandleError("suc:".$rootfoldername."/".$imgpath.$dir_file."/,".$fileName.",".$file_size);
				 		}else{
					        $this->HandleError("suc:".$imgpath.$dir_file."/,".$fileName.",".$file_size);
				        }
			 			exit(0);
			 		}
				}
				else {
					$this->HandleError("fai:创建目录失败");
					exit(0);
				}
			}
			exit(0);
	}
	function HandleError($message)
	{
		echo $message;
	}
	function CreateFirstName($file_extension )
	{
		$num=date('mdHis').rand(1,100);
		$fileName=$num.".".$file_extension;
		return $fileName;
	}
	function CreateNextName($file_extension,$file_dir)
	{
		$fileName_arr = scandir($file_dir,1);
		$fileName=$fileName_arr[0];
		$aa=floatval($fileName);
		$num=0;
		$num=(1+$aa);
		if(empty($aa)){
			$num = date('mdHis').rand(1,100);
		}
		return $num.".".$file_extension;
	}
	function createdatefilename($file_extension)
	{
		date_default_timezone_set('PRC');
		return date('mdHis').rand(1,100).".".$file_extension;
	}
	function create_folders($dir)
	{
       return is_dir($dir) or ($this->create_folders(dirname($dir)) and mkdir($dir, 0777));
    }
	function my_question_action()
	{
		$this->public_action();
		if($_GET['type']==0||$_GET['type']==''){
			$table="question";
		}elseif($_GET['type']==1){
			$table="answer";
		}elseif($_GET['type']==2){
			$table="answer_review";
		}
		$urlarr=array("c"=>"my_question","type"=>$_GET['type'],"page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$list = $this->get_page($table,"`uid`='".$this->uid."'  ORDER BY `add_time` DESC",$pageurl,"20");

		if($_GET['type']>0&&is_array($list)){
			foreach($list as $val){
				$qids[]=$val['qid'];
			}
			$question=$this->obj->DB_select_all("question","`id` in(".$this->pylode(',',$qids).")","`id`,`title`");
			foreach($list as $key=>$val){
				foreach($question as $value){
					if($val['qid']==$value['id']){
						$list[$key]['title']=$value['title'];
						$list[$key]['aid']=$val['id'];
					}
				}
			}
			if($_GET['type']=='1'){$this->yunset("typename",'回答');}else{$this->yunset("typename",'评论');}
		}
		$this->yunset("q_list",$list);
		$this->yunset("gettype",$_GET['type']);
		$this->yunset("js_def",7);
		$this->com_tpl('my_question');
	}
	function duihuan_action(){
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`pay`");
		$num=(int)$_POST['price_int'];
		$price=$num/$this->config['integral_proportion'];
		if($statis['pay']>$price){
			$this->obj->DB_update_all("company_statis","`pay`=`pay`-$price,`integral`=`integral`+$num","`uid`='".$this->uid."'");
			$this->insert_company_pay($price,2,$this->uid,'购买'.$num.$this->config['integral_pricename'],2,3);
			$this->obj->ACT_layer_msg("兑换成功！",9,"index.php?c=com");
		}else{
			$this->obj->ACT_layer_msg("余额不足！",8,"index.php?c=com");
		}
	}
	function autojob_action(){
		$jobid = (int)$_POST['jobid'];
		$alljob = (int)$_POST['alljob'];
		$autotype = (int)$_POST['autotype'];
		if($jobid>0)
		{
			$where['uid'] = $this->uid;
			if(!$alljob)
			{
				$where['id'] = $jobid;
			}
			if($autotype>0)
			{
				$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`integral`,`autotime`");
				if($statis['autotime']<time()){
					$this->obj->ACT_layer_msg("您的刷新功能已到期，如有需要请继续购买！",8,"index.php?c=job&w=1");
				}
			}else{

				$autotype = 0;
				$statis['autotime'] = 0;
			}
			$this->obj->update_once('company_job',array('autotype'=>$autotype,'autotime'=>$statis['autotime']),$where);
			$this->obj->ACT_layer_msg("刷新设置成功！",9,"index.php?c=job&w=1");

		}else{

			$this->obj->ACT_layer_msg("请选择有效的职位信息！",9,"index.php?c=job&w=1");
		}
	}

	function buyautojob_action(){

		$autocount = intval($_POST['autocount']);
		if($autocount>0)
		{
			$buyprice = ceil($autocount*$this->config['job_auto']);
			$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`integral`,`autotime`");
			if($statis['integral']>=$buyprice)
			{
				if($statis['autotime']>=time())
				{
					$autotime = $statis['autotime']+$autocount*86400;
				}else{
					$autotime = time()+$autocount*86400;
				}
				$integral = $statis['integral']-$buyprice;
				$this->obj->update_once('company_statis',array('integral'=>$integral,'autotime'=>$autotime),array('uid'=>$this->uid));
				$this->obj->update_once('company_job',array('autotime'=>$autotime),"`uid`='".$this->uid."' AND `autotype`>0");
				$this->insert_company_pay($buyprice,2,$this->uid,"购买职位自动刷新",1,9);
				$this->obj->ACT_layer_msg("购买成功，有效期至".date('Y-m-d',$autotime),9,"index.php?c=job&w=1");
			}else{
				$this->obj->ACT_layer_msg("您的".$this->config['integral_pricename']."余额不足，请先兑换或者充值！",8,"index.php?c=job&w=1");
			}

		}else{

			$this->obj->ACT_layer_msg("请填写一个有效的购买期限！",9,"index.php?c=job&w=1");
		}
	}
	function verify_action()
	{
		$info=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		if($_POST['email'])
		{
			if($info['linkmail']!="" && $info['linkmail']!=$_POST['email'])
			{
				echo 1;die;
			}
		}
		if($_POST['moblie'])
		{
			if($info['linktel']!="" && $info['linktel']!=$_POST['moblie'])
			{
				echo 1;die;
			}
		}
	}
}
?>