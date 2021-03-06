<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
class apply_controller extends common
{
	function index_action()
	{
		$where = "1";
		if($_GET['keyword'])
		{
			if($_GET['type']=="1")
			{
				$where.=" and `job_name` like '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=="2"){
				$where.=" and `com_name` like '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=="3"){
				$info=$this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%'","`uid`");
				if(is_array($info)){
					foreach ($info as $v){
						$uid[]=$v['uid'];
					}
				}
				$where.=" and `uid` in (".@implode(",",$uid).")";
			}elseif ($_GET['type']=="4"){
				$info=$this->obj->DB_select_all("resume_expect","`name` like '%".$_GET['keyword']."%'","`uid`");
				if(is_array($info)){
					foreach ($info as $v){
						$eid[]=$v['uid'];
					}
				}
				$where.=" and `eid` in (".@implode(",",$eid).")";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['sdate'])
		{
			$sdate=strtotime($_GET['sdate']);
			$where.=" and `datetime`>'$sdate'";
		}
		if($_GET['edate'])
		{
			$edate=strtotime($_GET['edate']);
			$where.=" and `datetime`<'$edate'";
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$list=$this->get_page("userid_job",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($list))
		{
			foreach($list as $v)
			{
				$uid[]=$v['uid'];
				$eid[]=$v['eid'];
				$com_id[]=$v['com_id'];
			}
			$member=$this->obj->DB_select_all("member","`uid` in (".@implode(",",$uid).")","username,uid");
			$resume=$this->obj->DB_select_all("resume_expect","`id` in (".@implode(",",$eid).")","name,id");
			$statis=$this->obj->DB_select_all("company_statis","`uid` in (".@implode(",",$com_id).")","rating,uid");
			foreach($list as $k=>$v)
			{
				foreach($member as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$list[$k]['username']=$val['username'];
					}
				}
				foreach($resume as $val)
				{
					if($v['eid']==$val['id'])
					{
						$list[$k]['resume']=$val['name'];
					}
				}
				foreach($statis as $val)
				{
					if($v['com_id']==$val['uid'])
					{
						$list[$k]['rating']=$val['rating'];
					}
				}
			}
		}
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/apply'));
	}
	function del_action()
	{
		$this->check_token();
	    if($_GET['del']){
	    	if(is_array($_GET['del']))
	    	{
	    		$del=@implode(",",$_GET['del']);
	    		$layer_status=1;
	    	}else{
	    		$del=$_GET['del'];
	    		$layer_status=0;
	    	}
			$this->obj->DB_delete_all("userid_job","`id` in (".$del.")","");
			$this->layer_msg( "邀请面试记录(ID:".$del.")删除成功！",9,$layer_status,$_SERVER['HTTP_REFERER']);
	   }else{
			$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
    	}
	}
}
?>