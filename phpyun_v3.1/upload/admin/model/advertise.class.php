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
class advertise_controller extends common{
	function public_action(){
		include_once("model/model/advertise_class.php");
	}
	function index_action(){
		$where = '1';
		if($_GET['class_id']){
			$where .=" AND `class_id`='".$_GET['class_id']."'";
			$urlarr['class_id']=$_GET['class_id'];
		}
		if($_GET['name']){
			$where .=" AND `ad_name` LIKE '%".$_GET['name']."%'";
			$urlarr['name']=$_GET['name'];
		}
		$where.=" order by id desc";
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$linkrows=$this->get_page("ad",$where,$pageurl,$this->config['sy_listnum']);
		$domain=$this->obj->DB_select_all("domain","1","`id`,`title`");
		$class = $this->obj->DB_select_all("ad_class");
		if(is_array($linkrows)){
			foreach($linkrows as $key=>$value){
				$start = @strtotime($value["time_start"]);
				$end = @strtotime($value["time_end"]." 23:59:59");
				$time = time();
				if(is_array($class)){
					foreach($class as $k=>$v){
						if($value["class_id"]==$v["id"]){
							$linkrows["$key"]["class_name"] = $v["class_name"];
						}
					}
				}
				if($value["is_check"]=="1"){
					$linkrows["$key"]["check"]="<font color='green'>已审核</font>";
				}else{
					$linkrows["$key"]["check"]="<font color='red'>未审核</font>";
				}
				switch($value["ad_type"]){
					case "word":$linkrows["$key"]["ad_typename"] ="文字广告";
					break;
					case "pic":$linkrows["$key"]["ad_typename"] ="图片广告";
					break;
					case "flash":$linkrows["$key"]["ad_typename"] ="FLASH广告";
					break;
				}
				if($value["time_start"]!="" && $start!="" &&($value["time_end"]==""||$end!="")){
					if($value["time_end"]=="" || $end>$time){
						if($start<$time){
							$linkrows["$key"]["type"]="<font color='green'>使用中..</font>";
						}elseif($start>$time && ($end>$time || $value["time"]=="")){
							$linkrows["$key"]["type"]="<font color='yellow'>广告暂未开始</font>";
						}
					}else{
						$linkrows["$key"]["type"]="<font color='red'>过期广告</font>";
						$linkrows["$key"]["is_end"]='1';
					}
				}else{
					$linkrows["$key"]["type"]="<font color='red'>无效广告</font>";
				}
				if(!empty($domain)){
					foreach($domain as $d_v){
						if($value['did']=='0'){
							$linkrows["$key"]["d_title"]='全站使用';
						}else if($value['did']==$d_v['id']){
							$linkrows["$key"]["d_title"]=$d_v['title'];
						}
					}
				}else{
					$linkrows["$key"]["d_title"]='全站使用';
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("class",$class);
		$this->yunset("linkrows",$linkrows);
		$this->yuntpl(array('admin/admin_advertise'));
	}
	function ad_add_action()
	{
		$class = $this->obj->DB_select_all("ad_class");
		$where=1;
		$domain = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		$this->yunset("domain",$domain);
		$this->yunset("class",$class);
		$this->yuntpl(array('admin/admin_advertise_add'));
	}
	function ad_saveadd_action()
	{
	 $this->public_action();
	 $adver = new advertise($this->obj);
		if($_FILES["ad_url"]["size"]>0)
	 	{
		 	if($_POST["ad_type"]=="flash")
		 	{
		 		$time = time();
				$flash_name = $time.rand(0,999).".swf";
		 		move_uploaded_file($_FILES['ad_url']['tmp_name'],APP_PATH."/upload/flash/$flash_name");
		 		$pictures = "../upload/flash/".$flash_name;
		 	}else{
		 		$upload = $this->upload_pic("../upload/adpic/");
		 		$pictures=$upload->picture($_FILES["ad_url"]);
		 	}
		}
	 $html = $adver->model_saveadd_action($_POST,$pictures);
	}
	function modify_action()
	{
		extract($_GET);
		$ad_info = $this->obj->DB_select_once("ad","`id`='$id'");
		$class = $this->obj->DB_select_all("ad_class");
		$where=1;
		$shell=$this->obj->DB_select_once("admin_user","`uid`='".$_SESSION['auid']."'");
		$where="`id` in (".$shell['domain'].")";
		$shelldomain=@explode(",",$shell['domain']);
		$this->yunset("shelldomain",$shelldomain);
		$domain = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		$this->yunset("domain",$domain);
		$this->yunset("class",$class);
		$this->yunset("ad_info",$ad_info);
		$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		$this->yuntpl(array('admin/admin_advertise_add'));
	}
	function modify_save_action()
	{
		$this->public_action();
		$adver = new advertise($this->obj);
		if($_FILES["ad_url"]["size"]>0)
	 	{
		 	if($_POST["ad_type"]=="flash")
		 	{
		 		$time = time();
				$flash_name = $time.rand(0,999).".swf";
		 		move_uploaded_file($_FILES['ad_url']['tmp_name'],APP_PATH."/upload/flash/".$flash_name);
		 		$pictures = "../upload/flash/".$flash_name;
		 	}else{
		 		$upload = $this->upload_pic("../upload/adpic/");
		 		$pictures=$upload->picture($_FILES["ad_url"]);
		 	}
		}
			$adver->model_modify_save_action($_POST,$pictures);
	}
	function del_ad_action()
	{
		$this->check_token();
		$this->public_action();
		$adver = new advertise($this->obj);
		if($_GET["id"]){
			$ad=$this->obj->DB_select_once("ad","`id`='".$_GET["id"]."'");
			if(is_array($ad)){
				$this->obj->unlink_pic($ad['pic_url']);
				@unlink($ad['flash_url']);
				$this->obj->DB_delete_all("ad","`id`='".$_GET["id"]."'");
			}
		}
		$adver->model_ad_arr_action();
		$this->layer_msg('广告(ID:'.$_GET["id"].')删除成功！',9,0,"index.php?m=advertise");
	}
	function ad_preview_action(){
		$ad=$this->obj->DB_select_once("ad","`id`='".$_GET['id']."'");
		if($ad_type=="word"){
			$ad['html']="<a href='".$ad['word_url']."'>".$ad['word_info']."</a>";
		}else if($ad['ad_type']=='pic'){
			if(@!stripos("ttp://",$ad['pic_url'])){
				$pic_url = str_replace("../",$this->config["sy_weburl"]."/",$ad['pic_url']);
			}
			$height = $width="";
			if($ad['pic_height']){
				$height = "height='".$ad['pic_height']."'";
			}
			if($ad['pic_width']){
				$width = "width='".$ad['pic_width']."'";
			}
			$ad['html']="<a href='".$ad['pic_src']."' target='_blank' rel='nofollow'><img src='".$pic_url."'  ".$height." ".$width." ></a>";
		}else if($ad['ad_type']=='flash'){
			if(@!stripos("ttp://",$ad['flash_url'])){
				$flash_url = str_replace("../",$this->config["sy_weburl"]."/",$ad['flash_url']);
			}
			$ad['html']="<object type='application/x-shockwave-flash' data='".$flash_url."' width='".$ad['flash_width']."' height='".$ad['flash_height']."'><param name='movie' value='".$flash_url."' /><param value='transparent' name='wmode'></object>";
		}
		if(@strtotime($ad['time_end']." 23:59:59")<time()){
			$ad['is_end']='1';
		}
		$ad['src']=$this->config['sy_weburl']."/plus/ad.php?classid=".$ad['class_id']."&id=".$ad['id'];
		$this->yunset("ad",$ad);
		$this->yuntpl(array('admin/admin_ad_preview'));
	}
	function ajax_check_action()
	{
		extract($_POST);
		$this->obj->DB_update_all("ad","`is_check`='$val'","`id`='$id'");
		$this->public_action();
		$adver = new advertise($this->obj);
		$adver->model_ad_arr_action();
		if($val=="1")
		{
			echo "<font color='green'>已审核</font>";
		}else{
			echo "<font color='red' >未审核</font>";
		}

	}
	function class_action(){
		if(trim($_POST['class_name'])){
			if($_POST["id"]){
				$nid=$this->obj->DB_update_all("ad_class","`class_name`='".iconv('utf-8','gbk',$_POST["class_name"])."',`orders`='".$_POST["orders"]."',`href`='".$_POST["href"]."'","`id`='".$_POST["id"]."'");
				$nid?$msg=1:$msg=2;echo $msg;die;
			}else if($_POST["id"]==''){
				$nid=$this->obj->DB_insert_once("ad_class","`class_name`='".iconv('utf-8','gbk',$_POST["class_name"])."',`orders`='".$_POST["orders"]."',`href`='".$_POST["href"]."'");
				$nid?$msg=1:$msg=2;echo $msg;die;
			}
		}
		$ad_class_list = $this->obj->DB_select_all("ad_class");
		if($_GET["ad_id"]){
			$ad_class = $this->obj->DB_select_once("ad_class","`id`='".$_GET["ad_id"]."'");
		}
		$this->yunset("ad_class",$ad_class);
		$this->yunset("ad_class_list",$ad_class_list);
		$this->yuntpl(array('admin/admin_ad_class'));
	}
	function delclass_action()
	{
		$this->check_token();
		extract($_GET);
		$ad = $this->obj->DB_select_once("ad","`class_id`='$id'");
		if(is_array($ad))
		{
			$this->layer_msg('该分类下还有广告，请清空后再执行删除！',8,0,"index.php?m=advertise&c=class");
		}else{
			$this->obj->DB_delete_all("ad_class","`id`='$id'");
			$this->layer_msg('广告类别(ID:'.$id.')删除成功！',9,0,"index.php?m=advertise&c=class");
		}

	}
	function cache_ad_action(){
		$this->public_action();
		$adver = new advertise($this->obj);
		$adver->model_ad_arr_action();
		$this->layer_msg("更新成功！",9,0,"index.php?m=advertise");
	}
	function ctime_action(){
		extract($_POST);

		$id=$this->obj->DB_update_all("ad","`time_end`=DATE_ADD(time_end,INTERVAL ".$endtime." DAY)","`id` IN (".$jobid.")");
		$this->public_action();
		$adver = new advertise($this->obj);
		$adver->model_ad_arr_action();
		$id?$this->obj->ACT_layer_msg("广告批量延期(ID:".$jobid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);

	}

}
?>