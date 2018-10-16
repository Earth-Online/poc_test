<?php
class comcert_controller extends common
{
	function index_action(){
		if($_GET["status"]==1){
			$where=" and a.`status`='1'";
			$urlarr["status"]='1';
		}else if($_GET["status"]==2){
			$where=" and a.`status`='0'";
			$urlarr["status"]='2';
		}
		if($_GET["keyword"]){
			$where.=" and b.`name` like '%".$_GET["keyword"]."%'";
			$urlarr["keyword"]=$_GET["keyword"];
		}
		if($_GET['order'])
		{
			$where.=" order by a.`".$_GET['t']."` ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by a.`id` desc";
		}
		include(LIB_PATH."page3.class.php");
		$limit=$this->config["sy_listnum"];
		$page=$_GET["page"]<1?1:$_GET["page"];
		$ststrsql=($page-1)*$limit;
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET["m"],$urlarr);
		$count=$this->obj->DB_select_alls("company_cert","company","a.`uid`=b.`uid` and a.`type`='3' $where","a.uid");
 		$num = count($count);
 		$page = new page($page,$limit,$num,$pageurl);
		$pagenav=$page->numPage();
		$rows=$this->obj->DB_select_alls("company_cert","company","a.`uid`=b.`uid` and a.`type`='3' $where $order limit $ststrsql,$limit","b.name,a.*");
		$this->yunset("pagenav",$pagenav);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_com_cert'));
	}
	function sbody_action(){
		$userinfo = $this->obj->DB_select_once("company_cert","`id`=".$_POST['pid'],"`statusbody`");
		echo $userinfo['statusbody'];die;
	}
	function status_action(){

		extract($_POST);
		$company=$this->obj->DB_select_once("company","`uid`='$uid'","`cert`,`linkmail`,`name`");
		if($status!="1"){
			$cert=@explode(",",$company["cert"]);
			foreach($cert as $v){
				if($v!="3"){
					$certid[]=$v;
				}
			}
			$cert=@implode(",",$certid);
		}else{
			$cert=$company["cert"].",3";
		}
		$this->obj->DB_update_all("company","`cert`='$cert'","`uid`='$uid'");
		$this->obj->DB_update_all("friend_info","`iscert`='$status'","`uid`=$uid");
		$id = @explode(",",$pid);
		if(is_array($id)){
			foreach($id as $value){
				$idlist[] = $value;
			}
			$aid = @implode(",",$idlist);
			$id=$this->obj->DB_update_all("company_cert","`status`='$status',`statusbody`='".$statusbody."'","`id` IN ($aid)");
			if($this->config['sy_email_comcert']=='1'){
				$this->send_msg_email(array("email"=>$company["linkmail"],"certinfo"=>$statusbody,"comname"=>$company["name"],"type"=>"comcert"));
			}
 			$id?$this->obj->ACT_layer_msg("审核(ID:".$uid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg(" 审核(ID:".$uid.")设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}

	function del_action(){

		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			$type=1;
		}else{
			$this->check_token();
			$linkid=$_GET['uid'];
			$type=0;
		}
		if($linkid==""){
			$this->layer_msg('请选择您要删除的数据！',8,1,$_SERVER['HTTP_REFERER']);
		}
		$listid=@explode(",",$linkid);
		foreach($listid as $val)
		{
			$company=$this->obj->DB_select_once("company","`uid`='".$val."'");
			$certs=@explode(",",$company['cert']);
			foreach($certs as $v)
			{
				if($v!="3"){
					$certid[]=$v;
				}
			}
			$dcert=@implode(",",$certid);
			$this->obj->DB_update_all("company","`cert`='$dcert'","`uid`='".$val."'");
		}
		$this->obj->DB_update_all("friend_info","`iscert`='0'","`uid` in (".$linkid.")");
	    $cert=$this->obj->DB_select_all("company_cert","`uid` in (".$linkid.") and `type`='3'","`check`");
	    if(is_array($cert))
	    {
	     	foreach($cert as $v)
	     	{
	     		$this->obj->unlink_pic("../".$v['check']);
	     	}
	     }
		$delid=$this->obj->DB_delete_all("company_cert","`uid` in (".$linkid.")  and `type`='3'","");
		$delid?$this->layer_msg('企业(UID:'.$linkid.')删除成功！',9,$type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$type,$_SERVER['HTTP_REFERER']);
	}
}

?>