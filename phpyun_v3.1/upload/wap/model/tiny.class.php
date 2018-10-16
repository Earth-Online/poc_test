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
class tiny_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		$this->yunset("title","微简历");
		$this->yuntpl(array('wap/tiny'));
	}
	function show_action()
	{
		$this->get_moblie();
		$this->yunset("title","微简历");
		$this->user_cache();
		$row=$this->obj->DB_select_once("resume_tiny","`id`='".$_GET[id]."'");
		$this->yunset("row",$row);
		$this->yuntpl(array('wap/tiny_show'));
	}
}
?>