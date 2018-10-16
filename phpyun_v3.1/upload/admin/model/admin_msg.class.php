<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
class admin_msg_controller extends common
{
	function index_action()
	{
		$where=1;
		if($_GET['keyword']!=""){
			if($_GET['searchtype']=="1"){
				$where.=" and `username` LIKE '%".$_GET['keyword']."%'";
			}elseif($_GET['searchtype']=="2"){
				$where.=" and `job_name` LIKE '%".$_GET['keyword']."%'";
			}elseif($_GET['searchtype']=="3"){
				$where.=" and `com_name` LIKE '%".$_GET['keyword']."%'";
			}elseif ($_GET['searchtype']=="4"){
			    $where.=" and `content` LIKE '%".$_GET['keyword']."%'";
			}elseif ($_GET['searchtype']=="5"){
			    $where.=" and `reply` LIKE '%".$_GET['keyword']."%'";
			}
			$page_url['keyword']=$_GET['keyword'];
			$page_url['searchtype']=$_GET['searchtype'];
		}
		if($_GET['type']){
			$where.=" and `type`='".$_GET['type']."'";
			$page_url['type']=$_GET['type'];
		}
		if($_GET['order']){
			$order=$_GET['order'];
		}else{
			$order="desc";
		}
		$page_url['order']=$_GET['order'];
		$page_url['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$page_url);
		$mes_list = $this->get_page("msg",$where." ORDER BY `id` ".$order,$pageurl,$this->config['sy_listnum']);
		$this->yunset("mes_list",$mes_list);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_msg'));
	}

	function reply_msg_action()
	{
		extract($_GET);
		include(PLUS_PATH."user.cache.php");
		$this->yunset("userdata",$userdata);
		if($id){
			$mes_info = $this->obj->DB_select_once("msg","`id`='$id'");
			$mes_info['class_name'] = $userclass_name[$mes_info['type']];
			$this->yunset("mes_info",$mes_info);
		}
		if($_POST['submit']){
			$this->obj->DB_update_all("msg","`reply`='".$_POST['reply']."',`status`='1'","`id`='$id'");
 			$this->obj->ACT_layer_msg("求职咨询(ID:".$id.")回复成功！",9,"index.php?m=admin_msg",2,1);

		}
		$this->yuntpl(array('admin/admin_msg_reply'));
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if(is_array($del)){
				$this->obj->DB_delete_all("msg","`id` in(".@implode(',',$del).")","");
	    		$this->layer_msg( "求职咨询(ID:".@implode(',',$del).")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("msg","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('求职咨询(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}