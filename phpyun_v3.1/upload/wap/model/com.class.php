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
	function index_action()
	{
		$this->get_moblie();
		$this->industry_cache();
		$this->job_cache();
		$this->yunset("title","职位搜索");
		$this->yuntpl(array('wap/com'));
	}
	function search_action()
	{
		$this->get_moblie();
		$this->industry_cache();
		$this->job_cache();
		$this->yunset("title","职位搜索");
		$this->yuntpl(array('wap/com'));
	}
	function view_action()
	{
		$this->get_moblie();
		$this->industry_cache();
		$this->com_cache();
		$this->job_cache();
		$this->city_cache();
		$job=$this->obj->DB_select_once("company_job","`id`='".$_GET['id']."'");
		$company=$this->obj->DB_select_once("company","`uid`='".$job['uid']."'");
		$this->yunset("job",$job);
		$this->yunset("company",$company);
		$this->yunset("title","职位详情");
		$this->yuntpl(array('wap/com_show'));
	}
	function sq_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1){

			$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','您还没有登录，请先登录！');
		}

		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		if(empty($resume))
		{
			$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','您还没有简历，请先添加简历！');
		}
		$row=$this->obj->DB_select_once("userid_job","`uid`='".$this->uid."' and `job_id`='".$_GET['id']."'");
		if(!empty($row))
		{
			$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','您已经申请过该职位，请不要重复申请！');
		}
		$info=$this->obj->DB_select_once("company_job","`id`='".$_GET['id']."'");
		$value['job_id']=$_GET['id'];
		$value['com_name']=$info['com_name'];
		$value['job_name']=$info['name'];
		$value['com_id']=$info['uid'];
		$value['uid']=$this->uid;
		$value['eid']=$resume['def_job'];
		$value['datetime']=mktime();
		$nid=$this->obj->insert_into("userid_job",$value);
		if($nid){
			$this->obj->DB_update_all('company_statis',"`sq_job`=`sq_job`+1","`uid`='".$value['com_id']."'");
			$this->obj->DB_update_all('member_statis',"`sq_jobnum`=`sq_jobnum`+1","`uid`='".$value['uid']."'");
			$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','申请成功！');
		}else{
			$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','申请失败！');
		}
	}
	function fav_action()
	{
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1)
		{
			$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','您还没有登录，请先登录！');
		}else{
			$rows=$this->obj->DB_select_all("fav_job","`uid`='".$this->uid."' and `job_id`='".$_GET['id']."'");
			if(is_array($rows)&&!empty($rows))
			{
				$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','您已经收藏过该职位，请不要重复收藏！');
			}
			$job=$this->obj->DB_select_once("company_job","`id`='".$_GET['id']."'");
			$data['job_id'] = $job['id'];
			$data['com_name'] = $job['com_name'];
			$data['job_name'] = $job['name'];
			$data['com_id'] = $job['uid'];
			$data['uid'] = $this->uid;
			$data['datetime'] = time();
			$nid=$this->obj->insert_into('fav_job',$data);

			if($nid){
				$this->obj->DB_update_all('member_statis',"`fav_jobnum`=`fav_jobnum`+1","`uid`='".$this->uid."'");
				$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','收藏成功！');
			}else{
				$this->wapheader('index.php?m=com&c=view&id='.$_GET[id].'&','收藏失败！');
			}
		}
	}
}
?>