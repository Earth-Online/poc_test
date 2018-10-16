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
class com_pl_controller extends common
{
	function index_action()
	{
		$where=1;
		if($_GET['keyword']!=""){
			if($_GET['searchtype']=="1"){
				$user=$this->obj->DB_select_all("member","`username` LIKE '%".$_GET['keyword']."%'");
				if(is_array($user)){
					foreach($user as $v){
						$userid[]=$v['uid'];
					}
					$where.=" and `uid` in (".@implode(",",$userid).")";
				}
			}elseif($_GET['searchtype']=="2"){
				$com=$this->obj->DB_select_all("company","`name` LIKE '%".$_GET['keyword']."%'");
				if(is_array($com)){
					foreach($com as $v){
						$comid[]=$v['uid'];
					}
					$where.=" and `cuid` in (".@implode(",",$comid).")";
				}
			}elseif ($_GET['searchtype']=="3"){
				$where.=" and `content` LIKE '%".$_GET['keyword']."%'";
			}elseif ($_GET['searchtype']=="4"){
			    $where.=" and `reply` LIKE '%".$_GET['keyword']."%'";
			}
			$page_url['keyword']=$_GET['keyword'];
			$page_url['searchtype']=$_GET['searchtype'];
		}
		if($_GET['order']){
			$order=$_GET['order'];
		}else{
			$order="desc";
		}
		$page_url['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$page_url);
		$mes_list = $this->get_page("company_msg",$where." ORDER BY `id` $order",$pageurl,$this->config['sy_listnum']);
		if(is_array($mes_list)){
			foreach($mes_list as $v){
				$uid[]=$v['uid'];
				$cuid[]=$v['cuid'];
			}
			$userlist=$this->obj->DB_select_all("member","`uid` in (".@implode(",",$uid).")");
			$comlist=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$cuid).")");
			foreach($mes_list as $k=>$v){

				$mes_list[$k]['content'] = str_replace('"',"",$v['content']);
				$mes_list[$k]['reply'] = str_replace('"',"",$v['reply']);

				foreach($userlist as $val){
					if($v['uid']==$val['uid']){
						$mes_list[$k]['username']=$val['username'];
					}
				}
				foreach($comlist as $val){
					if($v['cuid']==$val['uid']){
						$mes_list[$k]['com_name']=$val['name'];
					}
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("mes_list",$mes_list);
		$this->yuntpl(array('admin/admin_compl'));
	}

	function del_action(){
	    if($_POST['del']){
	    	$del=$_POST['del'];
	    	if($del){
	    		if(@is_array($del)){
					$del=@implode(',',$del);
		    	}
		    	$this->obj->DB_delete_all("company_msg","`id` in (".$del.")","");
	    		$this->layer_msg( "评论(ID:".$del.")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$this->check_token();
			$result=$this->obj->DB_delete_all("company_msg","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('评论(ID:'.$_GET['id'].')删除成功！',9):$this->layer_msg('删除失败！',8);
		}else{
			$this->layer_msg('非法操作！',3);
		}
	}
}