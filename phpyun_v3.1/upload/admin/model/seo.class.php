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
class seo_controller extends common{
	function index_action(){  
		$where="1";
		if(trim($_GET['keyword'])){
			$where.=" and `".$_GET['type']."` like '%".trim($_GET['keyword'])."%'";
			$urlarr["type"]=$_GET['type'];
			$urlarr["keyword"]=$_GET['keyword'];
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$seolist = $this->get_page("seo",$where." order by id desc",$pageurl,$this->config["sy_listnum"]);
		$this->yunset("get_type",$_GET);
		$this->yunset("seolist",$seolist);
		$this->yuntpl(array('admin/admin_list_seo'));
	}
	function SeoAdd_action()
	{
		include(CONFIG_PATH."db.data.php"); 
		$this->yunset("seoconfig",$arr_data["seoconfig"]);
		$list = $this->obj->DB_select_all("domain");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_add_seo'));
	}
	function Modify_action()
	{
		if($_GET["id"])
		{
			$seo_info = $this->obj->DB_select_once("seo","`id`='".$_GET["id"]."'");
			$affiliation=@explode(",",$seo_info["affiliation"]);
			$this->yunset("seo",$seo_info);
			$this->yunset("affiliation",$affiliation);
		}
		include(CONFIG_PATH."db.data.php"); 
		$this->yunset("seoconfig",$arr_data["seoconfig"]);
		$list = $this->obj->DB_select_all("domain");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_add_seo'));
	}
	function Save_action()
	{
		extract($_POST);
		$time = time();
		$affiliation=@implode(",",$affiliation);
		$value = "`seoname`='$seoname',`ident`='$ident',`title`='$title',`keywords`='$keywords',`description`='$description',`affiliation`='$affiliation',`time`='$time'";
		if($_POST["update"])
		{
			$this->obj->DB_update_all("seo",$value,"`id`='$id'");
			$this->cache_action();
			$msg = "SEO 修改成功！";
		}elseif($_POST["add"]){
			$this->obj->DB_insert_once("seo",$value);
			$this->cache_action();
			$msg = "SEO 添加成功！";
		} 
		$this->obj->ACT_layer_msg( $msg,9,"index.php?m=seo",2,1);
		$this->yuntpl(array('admin/admin_add_seo'));
	}
	function del_action(){
		
		$this->check_token();
		$id=$this->obj->DB_delete_all("seo","`id`='".$_GET["id"]."'");
		if($id)$this->cache_action();  
		$id?$this->layer_msg('SEO(ID:'.$_GET["id"].')删除成功！',9,0,"index.php?m=seo"):$this->layer_msg('SEO(ID:'.$_GET["id"].')删除失败！',8,0,"index.php?m=seo");

	}
	function cache_action(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->seo_cache("seo.cache.php");
	}
}