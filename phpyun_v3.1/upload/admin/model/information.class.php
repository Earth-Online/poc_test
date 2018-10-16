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
class information_controller extends common
{
	function index_action(){

		$this->yuntpl(array('admin/information'));
	}
	function save_action(){
		extract($_POST);
		if(trim($content)==''){
			$this->obj->ACT_layer_msg("请输入短信内容！",8,$_SERVER['HTTP_REFERER']);
		}
		$uidarr=array();
		if($all==4){
			$useremail=@explode(',',$userarr);
			if(is_array($useremail)){
				foreach($useremail as $v){
					$uidarr[]=$v;
				}
			}
		}else{
			$userrows=$this->obj->DB_select_all("member","`usertype`='".$all."'","moblie");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$uidarr[]=$v["moblie"];
				}
			}
		}
		if(is_array($uidarr)){
			if($this->config["sy_msguser"]=="" || $this->config["sy_msgpw"]=="" || $this->config["sy_msgkey"]==""){
				$this->obj->ACT_layer_msg("还没有配置短信！",8,$_SERVER['HTTP_REFERER']);
			}
			foreach($uidarr as $v){
				$msguser=$this->config["sy_msguser"];
				$msgpw=$this->config["sy_msgpw"];
				$msgkey=$this->config["sy_msgkey"];
				$this->obj->sendSMS($msguser,$msgpw,$msgkey,$v,$content);
			}
		}
		$this->obj->ACT_layer_msg("发送成功！多个用户发送短息会有延迟！",9,$_SERVER['HTTP_REFERER'],2,1);
	}
}
?>