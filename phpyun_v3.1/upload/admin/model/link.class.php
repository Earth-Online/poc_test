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
class link_controller extends common
{
	function index_action()
	{
		include(PLUS_PATH."domain_cache.php");
		extract($_GET);
		if($state=='1')
		{
			$where="link_state='1'";
			$urlarr['state']='1';
		}elseif($state=='2'){
			$urlarr['state']='2';
			$where="link_state='0'";
		}elseif($_GET['news_search']!=''){
			if ($_GET['type']=='1'){
				$where="`link_name` like '%".$_GET['s_news_id']."%' and `link_type`='1'";
			}elseif ($_GET['type']=='2'){
				$where="`link_name` like '%".$_GET['s_news_id']."%' and `link_type`='2'";
			}else{
			    $where="`link_name` like '%".$_GET['s_news_id']."%'";				
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['s_news_id']=$_GET['s_news_id'];
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
		$linkrows=$this->get_page("admin_link",$where,$pageurl,$this->config['sy_listnum']);
		foreach($linkrows as $k=>$v)
		{
			if(is_array($site_domain))
			{
				foreach($site_domain as $val)
				{
					if($v['domain']=='0')
					{
						$linkrows[$k]['host']='全域名使用';
					}else if($v['domain']==$val['id']){
						$linkrows[$k]['host']=$val['host'];
					}
				}
			}else{
				$linkrows[$k]['host']='全域名使用';
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("linkrows",$linkrows);
		$this->yuntpl(array('admin/admin_link_list'));
	}

	function add_action(){
		if((int)$_GET["id"]){
			$linkarr=$this->obj->DB_select_once("admin_link","id='".$_GET["id"]."'");
			$this->yunset("linkrow",$linkarr);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
		$where=1;
		$shell=$this->obj->DB_select_once("admin_user","`uid`='".$_SESSION['auid']."'");
		$where="`id` in (".$shell['domain'].")";
		$shelldomain=@explode(",",$shell['domain']);
		$this->yunset("shelldomain",$shelldomain);
		$domain = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		$this->yunset("domain",$domain);
		$this->yuntpl(array('admin/admin_link_add'));
	}
 	function del_action(){
		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			$layer_type=1;
		}else{
			$this->check_token();
			$linkid=(int)$_GET["id"];
			$layer_type=0;
		}
		$row=$this->obj->DB_select_all("admin_link","`id` in ($linkid) and `pic`!=''");
		if(is_array($row)){
			foreach($row as $v){
				$this->obj->unlink_pic("../".$v["pic"]);
			}
		}
		$delid=$this->obj->DB_delete_all("admin_link","`id` in ($linkid)","");
		$this->get_cache();
		$delid?$this->layer_msg('友情连接(ID:'.$linkid.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
 	function status_action(){
		extract($_GET);
		$value=$yesid?1:0;
		$id=$yesid?$yesid:$noid;
		$status=$yesid?"审核":"取消";
		if($id){
		$update=$this->obj->DB_update_all("admin_link","`link_state`='$value'","id='$id'");
		$this->get_cache();
 		$update?$this->layer_msg("友情连接(ID:".$id.")".$status."成功！",9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg($status."失败！",8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg("非法操作！",8,0,$_SERVER['HTTP_REFERER']);
		}
	}
 	function save_action(){
		extract($_POST);
		$upload=$this->upload_pic("../upload/link/","22");
 		if($link_add){
			if(preg_match("/[^\d-., ]/",$sorting)){
				$this->obj->ACT_layer_msg("请正确填写，排序是数字！",8,$_SERVER['HTTP_REFERER']);
			}else{
				if($sorting=="")
				{
					$sorting="0";
				}
				if($phototype==""){
					$phototype="0";
				}
				$value="`link_name`='".trim($title)."',";
				$value.="`link_url`='$url',";
				$value.="`link_type`='$type',";
				$value.="`tem_type`='$tem_type',";
				$value.="`img_type`='$phototype',";
				$value.="`domain`='$domain',";
				$value.="`link_sorting`='$sorting',";
				$value.="`link_state`='1',";
				$value.="`link_time`='".mktime()."'";
				if($phototype==1){
					$pictures=$upload->picture($_FILES['uplocadpic']);
					$value.=",`pic`='".str_replace("../","",$pictures)."'";
				}else{
					$value.=",`pic`='".$uplocadpic."'";
				}
				$nbid=$this->obj->DB_insert_once("admin_link",$value);
				$this->get_cache();
 				isset($nbid)?$this->obj->ACT_layer_msg("友情连接(ID:".$nbid.")添加成功！",9,"index.php?m=link",2,1):$this->obj->ACT_layer_msg("添加失败！",8,"index.php?m=link");
			}
		}
 		if($link_update){
			$value="`link_name`='".trim($title)."',";
			$value.="`link_url`='$url',";
			$value.="`link_type`='$type',";
			$value.="`tem_type`='$tem_type',";
			$value.="`img_type`='$phototype',";
			$value.="`domain`='$domain',";
			$value.="`link_sorting`='$sorting'";
			if($phototype==1){
				if($_FILES['uplocadpic']['tmp_name']!=""){
					$pictures=$upload->picture($_FILES['uplocadpic']);
					$value.=",`pic`='".str_replace("../","",$pictures)."'";
					$row=$this->obj->DB_select_once("admin_link","`id`='$id' and `pic`!=''");
					if(is_array($row)){
						$this->obj->unlink_pic("../".$row["pic"]);
					}
				}
			}else{
				$value.=",`pic`='".$uplocadpic."'";
			}
			$nbid=$this->obj->DB_update_all("admin_link",$value,"`id`='$id'");
			$lasturl=str_replace("&amp;","&",$lasturl);
			$this->get_cache();
			isset($nbid)?$this->obj->ACT_layer_msg("友情连接(ID:".$id.")修改成功！",9,$lasturl,2,1):$this->obj->ACT_layer_msg("修改失败！",8,$lasturl);
		}

	}
	function get_cache(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->link_cache("link.cache.php");
	}
}

?>