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
class admin_tiny_controller extends common
{
	function index_action(){
		extract($_GET);
		if($status=='1'){
			$where="status='1'";
			$urlarr['status']='1';
		}elseif($status=='2'){
			$where="status='0'";
			$urlarr['status']='2';
		}elseif($_GET['news_search']){
			if ($exp!=""){
			$where .=" `exp`=$exp ";
			$urlarr['exp']=$exp;
			}else{
				$where .=" 1 ";
			}
			if ($sex!=""){
				$where .=" and  `sex`=$sex";
				$urlarr['sex']=$sex;
			}
			if ($keyword){
				if ($type=='1'){
					$where .=" and `username` like '%".$keyword."%'";
				}elseif ($type=='2'){
					$where .=" and `job` like '%".$keyword."%'";
				}elseif ($type=='3'){
					$where .=" and `mobile` like '%".$keyword."%'";
				}
			}
			$urlarr['type']=$type;
			$urlarr['keyword']=$keyword;
			$urlarr['news_search']=$_GET['news_search'];
		}else{
			$where=1;
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("resume_tiny",$where,$pageurl,$this->config['sy_listnum']);
		include APP_PATH."/plus/user.cache.php";
		if(is_array($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]['sex']=$userclass_name[$v['sex']];
				$rows[$k]['exp']=$userclass_name[$v['exp']];
			}
		}
		$this->yunset("get_type", $_GET);
		$this->user_cache();
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_tiny'));
	}
	function show_action(){
		$rows=$this->obj->DB_select_once("resume_tiny","`id`='".$_GET['id']."'");
		$this->user_cache();
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_tiny_show'));
	}
	function status_action(){
		$this->obj->DB_update_all("resume_tiny","`status`='".$_POST['status']."'","`id` IN (".$_POST['allid'].")");
		echo $_POST['status'];die;
	}
	function ajax_action()
	{
		include APP_PATH."/plus/user.cache.php";
		$row=$this->obj->DB_select_once("resume_tiny","`id`='".$_GET['id']."'");
		$info['username']=iconv("gbk","utf-8",$row['username']);
		$info['sex']=iconv("gbk","utf-8",$userclass_name[$row['sex']]);
		$info['exp']=iconv("gbk","utf-8",$userclass_name[$row['exp']]);
		$info['mobile']=$row['mobile'];
		$info['qq']=$row['qq'];
		$info['job']=iconv("gbk","utf-8",$row['job']);
		$info['production']=iconv("gbk","utf-8",$row['production']);
		$info['time']=date("Y-m-d",$row['time']);
		$info['status']=$row['status'];
		echo json_encode($info);
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if(is_array($del)){
				$this->obj->DB_delete_all("resume_tiny","`id` in(".@implode(',',$del).")","");
				$this->layer_msg( "微简历(ID:".@implode(',',$del).")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("resume_tiny","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('微简历(ID:'.$_GET['id'].')删除成功！',9):$this->layer_msg('删除失败！',8);
		}else{
			$this->layer_msg('非法操作！',3);
		}
	}
}
?>