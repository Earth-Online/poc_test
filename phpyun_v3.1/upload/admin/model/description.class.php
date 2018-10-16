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
class description_controller extends common{
	function index_action()
	{
		if($_GET['order'])
		{
			if($_GET['order']=="desc")
			{
				$order=" order by `".$_GET['t']."` desc";
			}else{
				$order=" order by `".$_GET['t']."` asc";
			}
		}else{
			$order=" order by `id` desc";
		}
		$descrows=$this->obj->DB_select_all("description","1 $order");
		$this->yunset("descrows",$descrows);
		$this->yuntpl(array('admin/admin_description'));
	}
	function add_action(){
		$descrow=$this->obj->DB_select_once("description","`id`='".$_GET["id"]."'");
		$this->yunset("descrow",$descrow);
		$this->yuntpl(array('admin/admin_description_add'));
	}
	function save_action(){
		extract($_POST);
		$value="`name`='$name',";
		if($url){
			$urlarr=explode(".",$url);
			$arrnum=count($urlarr);
			if($urlarr[$arrnum-1]!="html"){
				$this->obj->ACT_layer_msg("请正确填写静态网页名称！",8,$_SERVER['HTTP_REFERER']);
			}
			if(substr($url,0,1)=="/"){
				$url=substr($url,1);
			}
		}
		$value.="`url`='$url',";
		$value.="`title`='$title',";
		$value.="`keyword`='$keyword',";
		$value.="`descs`='$description',";
		$value.="`top_tpl`='$top_tpl',";
		$value.="`top_tpl_dir`='$top_tpl_dir',";
		$value.="`footer_tpl`='$footer_tpl',";
		$value.="`footer_tpl_dir`='$footer_tpl_dir',";
		$value.="`ctime`='".mktime()."',";
		$value.="`sort`='$sort',";
		$value.="`is_nav`='$is_nav',";
		$content = str_replace("&amp;","&",html_entity_decode($content,ENT_QUOTES,"GB2312"));
		$value.="`content`='".$content."'";
		if(!$id){
			$descid=$this->obj->DB_insert_once("description",$value);
			$ids=$descid;
			$alert="添加";
		}else{
			$descid=$this->obj->DB_update_all("description",$value,"`id`='$id'");
			$ids=$id;
			$alert="更新";
		}
		$descid?$this->obj->ACT_layer_msg("独立页面(ID:".$ids.")".$alert."成功！",9,"index.php?m=description",2,1):$this->obj->ACT_layer_msg($alert."失败！",8,$_SERVER['HTTP_REFERER']);
	}
	function del_action(){
		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			foreach($del as $key=>$value){
				$info = $this->obj->DB_select_once("description","`id`='$value'");
				$filename = $info["url"];
				if(file_exists($filename)){
					@unlink($filename);
				}
				$info = "";
			}
			$layer_type='1';
		}else{
			$this->check_token();
			$linkid=$_GET["id"];
			$info = $this->obj->DB_select_once("description","`id`='$linkid'");
			$filename = APP_PATH."/".$info["url"];
			if(file_exists($filename)){
				@unlink($filename);
			}
			$layer_type='0';
		}
		$delid=$this->obj->DB_delete_all("description","`id` in ($linkid)","");
		$delid?$this->layer_msg('独立页面(ID:'.$linkid.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
	function make_action(){
		extract($_GET);
		if($id){
			$where="`id`='$id'";
		}else{
			$where="1";
		}
		$rows=$this->obj->DB_select_all("description",$where);
		if(@is_array($rows)){
			foreach($rows as $row){
				$url=$this->config["sy_weburl"]."/index.php?m=index&c=get&id=".$row[id];
				$fw=$this->obj->html("../".$row["url"],$url);
			}
		}
 		$fw?$this->layer_msg("生成成功！",9,0,$_SERVER['HTTP_REFERER']):$this->obj->layer_msg("生成失败！",8,0,$_SERVER['HTTP_REFERER']);
	}
}
?>