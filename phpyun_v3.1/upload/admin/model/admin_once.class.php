<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
class admin_once_controller extends common
{
	function index_action(){
		extract($_GET);
		if($status=='1'){
			$where="status='1'";
			$urlarr['status']='1';
		}elseif($status=='2'){
			$where="status='0'";
			$urlarr['status']='2';
		}elseif($status=='3'){
			$where="`edate`<$time ";
			$urlarr['status']='3';
		}elseif($_GET['keyword']!=""){
			if ($type=='1'){
				$where="`companyname` like '%".$_GET['keyword']."%'";
			}elseif ($type=='2'){
				$where="`title` like '%".$_GET['keyword']."%'";
			}elseif ($type=='3'){
				$where="`phone` like '%".$_GET['keyword']."%'";
			}elseif ($type=='4'){
				$where="`linkman` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$type;
			$urlarr['keyword']=$_GET['keyword'];
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
		$rows=$this->get_page("once_job",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($rows)&&$rows){
			foreach($rows as $key=>$val){
				if($val['edate']<mktime()){
					$rows[$key]['status']=2;
				}

			}
		}
		$this->yunset("rows", $rows);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_once'));
	}

	function ctime_action(){
		extract($_POST);
		$id=@explode(",",$onceids);
		if(is_array($id)){
			$posttime=$endtime*86400;
			$id=$this->obj->DB_update_all("once_job","`edate`=`edate`+'".$posttime."'","`id` in (".$onceids.")");
			$id?$this->obj->ACT_layer_msg("职位延期(ID:".$jobid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",3,$_SERVER['HTTP_REFERER']);
		}
	}
	function show_action(){
		$show=$this->obj->DB_select_all("once_job","`id`='".$_GET['id']."'");
		$this->yunset("show",$show[0]);
		$this->yuntpl(array('admin/admin_once_show'));
	}
	function status_action(){
		$this->obj->DB_update_all("once_job","`status`='".$_POST['status']."'","`id` IN (".$_POST['allid'].")");
		echo $_POST['status'];die;
	}
	function ajax_action()
	{
		include APP_PATH."/plus/user.cache.php";
		$row=$this->obj->DB_select_once("once_job","`id`='".$_GET['id']."'");
		$info['title']=iconv("gbk","utf-8",$row['title']);
		$info['mans']=$row['mans'];
		$info['require']=iconv("gbk","utf-8",$row['require']);
		$info['companyname']=iconv("gbk","utf-8",$row['companyname']);
		$info['phone']=$row['phone'];
		$info['linkman']=iconv("gbk","utf-8",$row['linkman']);
		$info['address']=iconv("gbk","utf-8",$row['address']);
		$info['time']=date("Y-m-d",$row['ctime']);
		$info['status']=$row['status'];
		$info['qq']=$row['qq'];
		$info['email']=$row['email'];
		$info['edate']=date("Y-m-d",$row['edate']);
		echo json_encode($info);
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if(is_array($del)){
				$this->obj->DB_delete_all("once_job","`id` in(".@implode(',',$del).")"," ");
				$this->layer_msg( "招聘(ID:".@implode(',',$del).")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的招聘！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("once_job","`id`='".$_GET['id']."'" );
			$result?$this->layer_msg('招聘(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>