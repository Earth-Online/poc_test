<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This System Powered by PHPYUN.com
 */
class qqconfig_controller extends common
{
	function index_action(){
		$this->yunset("config",$this->config);
		$this->yuntpl(array('admin/admin_qq_config'));
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
			$this->obj->ACT_layer_msg("ÐÞ¸Ä³É¹¦£¡",9,1,2,1);
		} 
	}
}

?>