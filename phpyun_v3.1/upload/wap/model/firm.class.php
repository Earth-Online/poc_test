<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ����������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class firm_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		$this->industry_cache();
		$this->yunset("title","��˾����");
		$this->yuntpl(array('wap/firm'));
	}
	function show_action()
	{
		$this->get_moblie();
		$this->industry_cache();
		$this->com_cache();
		$this->job_cache();
		$this->city_cache();
		$row=$this->obj->DB_select_once("company","uid='".$_GET['id']."'");
		$row['lastupdate']=date("Y-m-d",$row['lastupdate']);
		$this->yunset("row",$row);
		$this->yunset("title","��˾����");
		$this->yuntpl(array('wap/firm_show'));
	}
}
?>