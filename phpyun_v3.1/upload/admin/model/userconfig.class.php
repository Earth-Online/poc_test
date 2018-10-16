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
class userconfig_controller extends common
{

	function index_action(){
		$qy_rows=$this->obj->DB_select_all("company_rating","`category`=1 order by sort desc");
		$this->yunset("qy_rows",$qy_rows);
		$lt_rows=$this->obj->DB_select_all("company_rating","`category`=2 order by sort desc");
		$this->yunset("lt_rows",$lt_rows);
		if($_GET['category']=="1"||$_GET['category']=="2"){
			$where=" and`category`='".$_GET['category']."'";
		}
		$rows=$this->obj->DB_select_all("company_rating","1 $where order by sort desc");
		$this->yunset("rows",$rows);

		$this->yunset("config",$this->config);
		$this->yuntpl(array('admin/admin_user_config'));
	}
	function save_logo_action(){
		$upload=$this->upload_pic("../data/logo/");
		if (is_uploaded_file($_FILES['sy_member_icon']['tmp_name'])) {
			$logo_path = $this->logo_upload($_FILES['sy_member_icon'],$upload);
			$this->logo_reset("sy_member_icon",$logo_path);
		}
		if (is_uploaded_file($_FILES['sy_unit_icon']['tmp_name'])) {
			$mlogo_path = $this->logo_upload($_FILES['sy_unit_icon'],$upload);
			$this->logo_reset("sy_unit_icon",$mlogo_path);
		}
		if (is_uploaded_file($_FILES['sy_lt_icon']['tmp_name'])) {
			$llogo_path = $this->logo_upload($_FILES['sy_lt_icon'],$upload);
			$this->logo_reset("sy_lt_icon",$llogo_path);
		}
		if (is_uploaded_file($_FILES['sy_friend_icon']['tmp_name'])) {
			$flogo_path = $this->logo_upload($_FILES['sy_friend_icon'],$upload);
			$this->logo_reset("sy_friend_icon",$flogo_path);
		}
		$this->web_config();
		$this->obj->ACT_layer_msg("会员头像配置设置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
	}
	function logo_upload($picurl,$upload){
		$pictures=$upload->picture($picurl);
		$pic = str_replace("../data/logo","data/logo",$pictures);
		return $pic;
	}
	function logo_reset($name,$value){
		$logo = $this->obj->DB_select_once("admin_config","`name`='$name'");
		if(is_array($logo)){
			$this->obj->unlink_pic("../".$logo[config]);
			$this->obj->DB_update_all("admin_config","`config`='$value'","`name`='$name'");
		}else{
			$this->obj->DB_insert_once("admin_config","`config`='$value',`name`='$name'");
		}
	}
	function save_action(){
 		if($_POST["config"]){
		 unset($_POST["config"]);
		   foreach($_POST as $key=>$v){
		    	$config=$this->obj->DB_select_num("admin_config","`name`='$key'");
			   if($config==false){
				$this->obj->DB_insert_once("admin_config","`name`='$key',`config`='".iconv("utf-8", "gbk", $v)."'");
			   }else{
					$this->obj->DB_update_all("admin_config","`config`='".iconv("utf-8", "gbk", $v)."'","`name`='$key'");

				   }
			 }
		 $this->web_config();
		 $this->obj->ACT_layer_msg("会员配置修改成功！",9,1,2,1);
		}
	}

	function comclass_action(){
		$row=$this->obj->DB_select_once("company_rating","`id`='".$_GET["id"]."'");
		$this->yunset("row",$row);
		if($_GET["lt"]==1){
			$this->yuntpl(array('admin/admin_ltclass_add'));
		}else{
			$this->yuntpl(array('admin/admin_comclass_add'));
		}
	}

	function del_action(){
		$this->check_token();
		$nid=$this->obj->DB_delete_all("company_rating","`id`='".$_GET["id"]."'");
 		$nid?$this->layer_msg('会员等级（ID：'.$_GET["id"].'）删除成功！',9):$this->layer_msg('删除失败！',8);
	}
	function saveclass_action(){
		if($_POST["useradd"]){
			$id=$_POST["id"];
			unset($_POST["useradd"]);
			unset($_POST["id"]);
			$where["id"]=$id;
			if(is_uploaded_file($_FILES['com_pic']['tmp_name'])){
				$upload=$this->upload_pic("../upload/compic/");
				$pictures=$upload->picture($_FILES['com_pic']);
				$pic = str_replace("../upload","upload",$pictures);
			}
			if(!$id){
				$_POST["com_pic"]=$pic;
				$nid=$this->obj->insert_into("company_rating",$_POST);
				$name="会员等级（ID：".$nid."）添加";
			}else{
				if($pic!=""){$_POST["com_pic"]=$pic;};
				$nid=$this->obj->update_once("company_rating",$_POST,$where);
				$name="会员等级（ID：".$id."）更新";
			}
		}
			$nid?$this->obj->ACT_layer_msg($name."成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg($name."失败！",8,$_SERVER['HTTP_REFERER']);
		}
	function jihuo_action(){
		$nid=$this->obj->DB_update_all("member","email_status='1'","usertype='1'");
		echo $nid;
	}
}

?>