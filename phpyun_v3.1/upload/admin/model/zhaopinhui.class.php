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
class zhaopinhui_controller extends common
{
	function index_action()
	{
		extract($_GET);
		$where="1";
		if($status=='0')
		{
			$where.=" and `starttime`>'".date("Y-n-j")."'";
		}elseif($status=='1'){
			$where.=" and `starttime`<'".date("Y-n-j")."' and `endtime`>'".date("Y-n-j")."'";
		}elseif($status=='2'){
			$where.=" and `endtime`<'".date("Y-n-j")."'";
		}
		$urlarr["status"]=$status;
		if($_GET['news_search'])
		{
			if ($_GET['type']=='2'){
				$where.=" and `address` like '%".$_GET['keyword']."%'";
			}else{
				$where.=" and `title` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
			$urlarr['news_search']=$_GET['news_search'];
		}
		$where.=" order by id desc";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$m,$urlarr);
		$rows=$this->get_page("zhaopinhui",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($rows))
		{
			foreach($rows as $key=>$v)
			{
				$rows[$key]['comnum']=$this->obj->DB_select_num("zhaopinhui_com","`zid`='".$v['id']."' ");;
				$rows[$key]['booking']=$this->obj->DB_select_num("zhaopinhui_com","`zid`='".$v['id']."' and `status`='0'");;
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_zhaopinhui_list'));
	}

	function add_action()
	{
		if((int)$_GET['id'])
		{
			$linkarr=$this->obj->DB_select_once("zhaopinhui","id='".$_GET['id']."'");
			$this->yunset("linkrow",$linkarr);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
		$this->yuntpl(array('admin/admin_zhaopinhui_add'));
	}
	//删除链接
	function del_action(){
	
		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			$layer_msg=1;
		}else{
			$this->check_token();
			$linkid=(int)$_GET["id"];
			$layer_msg=0;
		}
		$row=$this->obj->DB_select_all("zhaopinhui_pic","`zid` in ($linkid)");
		if(is_array($row)){
			foreach($row as $v){
				$this->obj->unlink_pic("..".$v["pic"]);
			}
		}
		$delid=$this->obj->DB_delete_all("zhaopinhui","`id` in (".$linkid.")","");
 		$delid?$this->layer_msg('招聘会(ID:'.$linkid.')删除成功！',9,$layer_msg,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_msg,$_SERVER['HTTP_REFERER']);
	}
	//保存信息
	function save_action(){
		extract($_POST);
		$id=$_POST["id"];
		unset($_POST["update"]);
		unset($_POST["add"]);
		unset($_POST["id"]);
		 $_POST["body"]=str_replace("&amp;","&",html_entity_decode($_POST["body"],ENT_QUOTES,"GB2312"));
		 $_POST["media"]=str_replace("&amp;","&",html_entity_decode($_POST["media"],ENT_QUOTES,"GB2312"));
		 $_POST["packages"]=str_replace("&amp;","&",html_entity_decode($_POST["packages"],ENT_QUOTES,"GB2312"));
		 $_POST["booth"]=str_replace("&amp;","&",html_entity_decode($_POST["booth"],ENT_QUOTES,"GB2312"));
		 $_POST["participate"]=str_replace("&amp;","&",html_entity_decode($_POST["participate"],ENT_QUOTES,"GB2312"));
		//添加
		if($add){
			$_POST["ctime"]=mktime();
			$_POST["status"]="0";
			$nbid=$this->obj->insert_into("zhaopinhui",$_POST);
			isset($nbid)?$this->obj->ACT_layer_msg("招聘会(ID:$nbid)添加成功！",9,"index.php?m=zhaopinhui",2,1):$this->obj->ACT_layer_msg("招聘会(ID:$nbid)添加失败！",8,"index.php?m=zhaopinhui");
		}
		//更新
		if($update){
			$where["id"]=$id;
			unset($_POST["lasturl"]);
			$nbid=$this->obj->update_once("zhaopinhui",$_POST,$where);
			$lasturl=str_replace("&amp;","&",$lasturl);
 			isset($nbid)?$this->obj->ACT_layer_msg("招聘会(ID:$id)修改成功！",9,$lasturl,2,1):$this->obj->ACT_layer_msg("招聘会(ID:$id)修改失败！",8,$lasturl);
		}
	}
	function upload_action(){
		extract($_GET);
		if($editid){
			$editrow=$this->obj->DB_select_once("zhaopinhui_pic","`id`='".$editid."'");
			$this->yunset("pic",substr($editrow['pic'],(strrpos($editrow['pic'],'/')+1)));
			$this->yunset("editrow",$editrow);
			$id=$editrow["zid"];
		}
		$row=$this->obj->DB_select_once("zhaopinhui","`id`='".$id."'");
		$this->yunset("row",$row);
		$urlarr["c"]=$c;
		$urlarr["id"]=$id;
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$m,$urlarr);
		$where=" zid='".$id."'";
		$rows=$this->get_page("zhaopinhui_pic",$where,$pageurl,"13");
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_zhaopinhui_upload'));
	}
	function uploadsave_action(){
		extract($_POST);
		$id=$_POST["id"];
		unset($_POST["update"]);
		unset($_POST["add"]);
		unset($_POST["id"]);
		if($add){
			if($_POST['pic']){
				$_POST["pic"]=str_replace($this->config['sy_weburl'],"",$_POST['pic']);
			}else{
				$this->obj->ACT_layer_msg("缩略图不能为空！",8,$_SERVER['HTTP_REFERER']);
			}
			$_POST["is_themb"]=0;
			$nbid=$this->obj->insert_into("zhaopinhui_pic",$_POST);
			isset($nbid)?$this->obj->ACT_layer_msg("添加成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("添加失败！",8,$_SERVER['HTTP_REFERER']);
		}
		if($update){
			if($_POST['pic']){
				$row=$this->obj->DB_select_once("zhaopinhui_pic","`id`='".$id."'");
				$_POST["pic"]=str_replace($this->config['sy_weburl'],"",$_POST['pic']);
				if($_POST['pic']!=$row['pic']){  
					$this->obj->unlink_pic("..".$row["pic"]);
				} 
			}
			$where["id"]=$id;
			$nbid=$this->obj->update_once("zhaopinhui_pic",$_POST,$where);
			isset($nbid)?$this->obj->ACT_layer_msg("修改成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("修改失败！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function pic_action(){
		
		if($_GET['sid']){
			
			$row=$this->obj->DB_select_once("zhaopinhui_pic","`id`='".$_GET['sid']."'");
			$nbidone=$this->obj->DB_update_all("zhaopinhui_pic","`is_themb`='0'","`zid`='".$row["zid"]."'");
			$nbid=$this->obj->DB_update_all("zhaopinhui_pic","`is_themb`='1'","`id`='".$_GET['sid']."'");
			$nbid2=$this->obj->DB_update_all("zhaopinhui","`pic`='".$row["pic"]."'","`id`='".$row["zid"]."'");
 			isset($nbidone) && isset($nbid) && isset($nbid2)?$this->layer_msg("修改成功！",9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg("修改失败！",8,0,$_SERVER['HTTP_REFERER']);
		}
		if($_GET['delid']){
			$this->check_token();
			$row=$this->obj->DB_select_once("zhaopinhui_pic","`id`='".$_GET['delid']."'");
			if($row["is_themb"]==1){
				$nbid2=$this->obj->DB_update_all("zhaopinhui","`pic`=''","`id`='".$row["zid"]."'");
			}
			$row=$this->obj->DB_select_once("zhaopinhui_pic","`id`='".$_GET['delid']."'");
			$this->obj->unlink_pic("..".$row["pic"]);
			$delid=$this->obj->DB_delete_all("zhaopinhui_pic","`id`='".$_GET['delid']."'");
 			$delid?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
	function com_action(){
		extract($_GET);

		$where="1";
		$where.=" and `zid`='".$id."'";
		$urlarr["id"]=$id;
		if($status){

			$where.=" and `status`='$status'";
			$urlarr["status"]=$status;
		}else{
			$where.=" and `status`='0'";
			$urlarr["status"]='0';
		}

		$where.=" order by id desc";
		$urlarr["c"]=$c;
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$m,$urlarr);
		$rows=$this->get_page("zhaopinhui_com",$where,$pageurl,"13");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
				$jobid[]=$v['jobid'];
			}
			$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$uid).")","`uid`,`name`");
			$company_job=$this->obj->DB_select_all("company_job","`id` in (".@implode(",",$jobid).")","`name`,`id`");
			foreach($rows as $k=>$v)
			{
				foreach($company as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$rows[$k]['comname']=$val['name'];
					}
				}
				$jobids=array();
				$jobname=array();
				$jobids=@explode(",",$v['jobid']);
				foreach($company_job as $val)
				{
					foreach($jobids as $value)
					{
						if($value==$val['id'])
						{
							$url=$this->config['sy_weburl']."/".$this->Url("index","com",array("c"=>"comapply","id"=>$val['id']),"1");
							$jobname[]='<a target="_blank" href="'.$url.'">'.$val['name'].'</a>';
						}
					}
					$rows[$k]['jobname']=@implode(",",$jobname);
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_zhaopinhui_com'));
	}
	function sbody_action(){
		$zhaopinhui_com=$this->obj->DB_select_once("zhaopinhui_com","`id`='".$_POST['id']."'",'statusbody');
		echo $zhaopinhui_com['statusbody'];die;
	}
		//删除链接
	function delcom_action(){
	
		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			$layer_type=1;
		}else{
			$this->check_token();
			$linkid=(int)$_GET["delid"];
			$layer_type=0;
		}
		$delid=$this->obj->DB_delete_all("zhaopinhui_com","`id` in (".$linkid.")"," ");
		$delid?$this->layer_msg('招聘会(ID:'.$linkid.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
	function status_action(){
		extract($_POST);
		$id = @explode(",",$pid);
		//$status //0未审核,1通过，2已过期，3未通过
		if(is_array($id)){
			foreach($id as $value){
				$idlist[] = $value;
			}
			$aid = @implode(",",$idlist);
			$id=$this->obj->DB_update_all("zhaopinhui_com","`status`='$status',`statusbody`='".$statusbody."'","`id` IN ($aid)");
 			$id?$this->obj->ACT_layer_msg("设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}

?>