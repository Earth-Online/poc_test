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
class admin_job_controller extends common
{
	function index_action(){
		$position=$this->obj->DB_select_all("job_class","`keyid`='0' order by sort asc");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_job'));
	}
	function save_action(){
		if(!empty($_POST['position'])){
			if(!is_array($this->obj->DB_select_once("job_class","`name`='".$_POST['position']."'"))){
				$value="`name`='".iconv('utf-8','gbk',trim($_POST["position"]))."',";
				if($_POST['ctype']=='1'){
					$value.="`keyid`='0',";
				}
				if($_POST['ctype']=='2'){
					$value.="`keyid`='".$_POST['nid']."',";
				}
				if($_POST['ctype']=='3'){
					$value.="`keyid`='".$_POST['keyid']."',";
				}
				$value.="`sort`='".$_POST['sort']."'";
				$add=$this->obj->DB_insert_once("job_class",$value);
				$this->cache_action();
				$add?$msg=3:$msg=4;
			}else{
				$msg=2;
			}
		}else{
			$msg=1;
		}echo $msg;die;
	}
	function ajaxjob_action(){
		extract($_GET);
		if($id!=""){
		    $jobs=$this->obj->DB_select_all("job_class","`keyid`=$id");
			if(is_array($jobs)){
				$html .= "<select name='keyid'>";
				if($ajax=="1"){
					$html .= "<option value=\"\">==请选择==</option>";
				}
				foreach($jobs as $key=>$v){
					$html .= "<option value='".$v['id']."'>".$v['name']."</option>";
				}
				$html .= "<select>";
				echo $html;
			 	die;
			}die;
		}
	}
	function up_action(){
		if((int)$_GET['id']){
			$id=(int)$_GET['id'];
			$onejob=$this->obj->DB_select_once("job_class","`id`='".$_GET['id']."'");
			$twojob=$this->obj->DB_select_all("job_class","`keyid`='".$_GET['id']."' order by sort asc","id,sort");
			if(is_array($twojob)){
				foreach($twojob as $key=>$v){
					$val[]=$v['id'];
					$root_arr = @implode(",",$val);
				}
			}
			$jobs=$this->obj->DB_select_all("job_class","`keyid`='".$_GET['id']."' or `keyid` in ($root_arr) order by sort asc");
			$a=0;
			if(is_array($jobs)){
				foreach($jobs as $key=>$v){
					if($v['keyid']==$id){
						$twojob[$a]['id']=$v['id'];
						$twojob[$a]['sort']=$v['sort'];
						$twojob[$a]['name']=$v['name'];
						$a++;
					}else{
						$threejob[$v['keyid']][]=$v;
					}
				}
			}
			$this->yunset("id",$id);
			$this->yunset("onejob",$onejob);
			$this->yunset("twojob",$twojob);
			$this->yunset("threejob",$threejob);
		}
		$position=$this->obj->DB_select_all("job_class","`keyid`='0'");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_job'));
	}
	function upp_action(){

		if($_POST['update']){
			if(!empty($_POST['position'])){
				$value="`name`='".$_POST['position']."',`sort`='".$_POST['sort']."'";
				$where="`id`='".$_POST['id']."'";
				$up=$this->obj->DB_update_all("job_class",$value,$where);
				$this->cache_action();
 		        $up?$this->obj->ACT_layer_msg("职位类别(ID:".$_POST['id'].")更新成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("添加失败！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("请正确填写你要更新的职位！",8,$_SERVER['HTTP_REFERER']);
			}
		}
		$this->yuntpl(array('admin/admin_job'));
	}
	function del_action()
	{
		if((int)$_GET['delid'])
		{
			$this->check_token();
			$del=$this->obj->DB_delete_all("job_class","`id`='".$_GET['delid']."' or `keyid`='".$_GET['delid']."'","");
			$this->cache_action();
			$del?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['del'])
		{
			$del=@implode(",",$_POST['del']);
			$id=$this->obj->DB_delete_all("job_class","`id` in (".$del.") or `keyid` in (".$del.")","");
			$this->cache_action();
			isset($id)?$this->layer_msg('删除成功！',9,1,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,1,$_SERVER['HTTP_REFERER']);
		}
		$this->yuntpl(array('admin/admin_job'));
	}
	function sclass_action(){
		include(PLUS_PATH."job.cache.php");
		if(is_array($job_type[$_GET['pid']])){
			$html="";
			foreach($job_type[$_GET['pid']] as $val){
				$html.="<option value=\"".$val."\">".$job_name[$val]."</option>";
			}
		}else{
			$html="<option value=\"\">--暂无子类--</option>";
		}
		echo $html;die;
	}
	function cache_action(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->job_cache("job.cache.php");
	}
	function move_action(){
		extract($_GET);
		if($keyid==""){
			$move = $this->obj->DB_update_all("job_class","`keyid`=$nid","`id`=$pid");
		}else{
			$move = $this->obj->DB_update_all("job_class","`keyid`=$keyid","`id`=$pid");
		}
 		$move?$this->obj->ACT_layer_msg("职位类别(ID:".$pid.")移动成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("移动失败！",8,$_SERVER['HTTP_REFERER']);
	}
	function ajax_action()
	{
		if($_POST['sort'])
		{
			$this->obj->DB_update_all("job_class","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
		}
		if($_POST['name'])
		{
			$_POST['name']=iconv("UTF-8","gbk",$_POST['name']);
			$this->obj->DB_update_all("job_class","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
		}
		$this->cache_action();echo '1';die;
	}
}

?>