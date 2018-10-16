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
class email_controller extends common{
	function index_action(){
		$this->yuntpl(array('admin/admin_send_email'));
	}
	function send_action(){
		extract($_POST);
		if($email_title==''||$content==''){
			$this->obj->ACT_layer_msg("邮件标题均不能为空！",8,$_SERVER['HTTP_REFERER']);
		}
		$emailarr=array();
		if(@in_array(1,$all)){
			$userrows=$this->obj->DB_select_all("member","`usertype`='1'","email");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$emailarr[]=$v["email"];
				}
			}
		}
		if(@in_array(2,$all)){
			$userrows=$this->obj->DB_select_all("member","`usertype`='2'","email");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$emailarr[]=$v["email"];
				}
			}
		}
		if(@in_array(4,$all)){
			$userrows=$this->obj->DB_select_all("member","`usertype`='3'","email");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$emailarr[]=$v["email"];
				}
			}
		}
		if(@in_array(3,$all)){
			$useremail=@explode(',',$email_user);
			if(is_array($useremail)){
				foreach($useremail as $v){
					$emailarr[]=$v;
				}
			}
		}
		if(!count($emailarr)){
			$this->obj->ACT_layer_msg("没有符合条件的邮箱，请先检查！",8,$_SERVER['HTTP_REFERER']);
		}
		set_time_limit(10000);
		$emailid=$this->send_email($emailarr,$email_title,$content,true);

	}
}

?>