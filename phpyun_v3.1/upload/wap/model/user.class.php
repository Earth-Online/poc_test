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
class user_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		$this->industry_cache();
		$this->job_cache();
		$this->yunset("title","找人才");
		$this->yuntpl(array('wap/user'));
	}
	function search_action()
	{
		$this->get_moblie();
		$this->industry_cache();
		$this->job_cache();
		$this->yunset("title","找人才");
		$this->yuntpl(array('wap/user'));
	}
	function show_action()
	{
		$this->get_moblie();
		$id=$_GET[id];
		$user_jy=$this->obj->DB_select_once("resume_expect","`id`='".$id."'");
		$user=$this->obj->DB_select_once("resume","`r_status`!='2' and `uid`='".$user_jy[uid]."'");
		if(is_array($user_jy)||is_array($user)){
			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/user.cache.php";
			include APP_PATH."/plus/industry.cache.php";

			if($this->config["user_name"]==3){
				$user["username_n"] = "NO.".$user_jy["id"];
			}elseif($this->config["user_name"]==2){
				if($user["sex"]=='6'){
					$user["username_n"] = mb_substr($user["name"],0,2)."先生";
				}else{
					$user["username_n"] = mb_substr($user["name"],0,2)."女士";
				}
			}else{
				$user["username_n"] = $user["name"];
			}

			if($_COOKIE['usertype']=='2'){
				$my_down=$this->obj->DB_select_all("down_resume","`comid`='".$_COOKIE['uid']."'","uid");
			}
			if(!empty($my_down)){
				foreach($my_down as $m_k=>$m_v){
					$my_down_uid[]=$m_v['uid'];
				}
			}
			if($this->config['sy_usertype_1']=='1'){
				if(in_array($user['uid'],$my_down_uid)==false ||$user['resume_photo']==""){
					$user['resume_photo']='/upload/user/ltphoto.jpg';
				}
			}else if($user['resume_photo']==""){
				$user['resume_photo']='/upload/user/ltphoto.jpg';
			}

			$user['user_sex']=$userclass_name[$user['sex']];
			$user['user_exp']=$userclass_name[$user["exp"]];
			$user['useredu']=$userclass_name[$user['edu']];
			$user['user_city_one']=$city_name[$user['province']];
			$user['user_city_two']=$city_name[$user['city']];
			$user['user_city_three']=$city_name[$user['three_city']];
			$user['user_cityid_one']=$city_name[$user['provinceid']];
			$user['user_cityid_two']=$city_name[$user['cityid']];
			$user['user_cityid_three']=$city_name[$user['three_cityid']];
			$user['age']=date("Y")-$a;
			$user['salary']=$userclass_name[$user_jy['salary']];
			$user['hy']=$industry_name[$user_jy['hy']];
			$user['lastupdate']=date("Y-m-d",$user_jy['lastupdate']);
			$user['r_name'] = $user_jy['name'];
			$user['doc'] = $user_jy['doc'];
			$user['hits']=$user_jy['hits'];
			$user['id']=$id;
			$jy=@explode(",",$user_jy['job_classid']);
			if(@is_array($jy)){
				foreach($jy as $v){
					$jobname[]=$job_name[$v];
				}
				$user['jobname']=@implode(",",$jobname);
			}
			if($user_jy['doc']){
				$user_doc=$this->obj->DB_select_once("resume_doc","`eid`='".$user['id']."'");
			}else{
				$user_work=$this->obj->DB_select_all("resume_work","`eid`='$user_jy[id]'");
			}
		}
		if($this->uid==$user['uid'] && $this->username && $_COOKIE["usertype"]==1){
			$user['m_status']=1;
		}
		if($this->uid && $this->username && ($_COOKIE["usertype"]==2 || $_COOKIE["usertype"]==3)){
			$row=$this->obj->DB_select_once("down_resume","`eid`='$id' and comid='".$this->uid."'");
			if(is_array($row)){
				$user['m_status']=1;
				$user['username_n'] = $user["name"];
			}
		}
		if($_GET["look"]){
			$user['m_status']=1;
		}
		$user['user_doc']=$user_doc;
		$user['user_jy']=$user_jy;
		$user['user_work']=$user_work;
		$data['resume_username']=$user['username_n'];
		$data['resume_city']=$user['user_city_one'].",".$user['user_city_two'];
		$data['resume_job']=$user['hy'];
		$this->yunset("Info",$user);
		$this->yunset("title","找人才");
		$this->yuntpl(array('wap/user_show'));
	}
}
?>