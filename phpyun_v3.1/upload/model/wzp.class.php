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
class wzp_controller extends common
{
	function index_action(){

 		$company = $this->obj->DB_select_all("company","1","`uid`,`name`,`logo`");
 		$iwebmx = $this->obj->DB_select_all("iweb_wzplist","`status`='1' group by uid order by countid desc limit 8","`uid`,count(uid)as countid");
		$iwebmx = $this->comname($iwebmx,$company);
		$this->yunset("iwebmx",$iwebmx);
 		$hotcomment = $this->hot($company,"mcount");
		$this->yunset("hotcomment",$hotcomment);
 		$hotreposts = $this->hot($company,"count");
		$this->yunset("hotreposts",$hotreposts);
 		$urlarr=array("page"=>"{{page}}");
		$pageurl=$this->url("index",$_GET[M],$urlarr,"1");
		$linkrows=$this->get_page("iweb_wzplist"," `status`='1' order by `id` desc",$pageurl,"10");
		$linkrows = $this->comname($linkrows,$company); 
		$this->yunset("linkrows",$linkrows);
		$this->seo("wzp");
		$this->yun_tpl(array('index'));
	}

 	function comname($linkrows,$company){
		if(is_array($linkrows)){
			foreach($linkrows as $k=>$v){
				if(is_array($company)){
					foreach($company as $key=>$value){
						if($v['uid']==$value['uid']){
							$linkrows[$k]['company'] = $value['name'];
							$linkrows[$k]['comlogo'] = $value['logo'];
						}
					}
				}
			}
		}
		return $linkrows;
	}
	function hot($company,$order)
	{
		$hot = $this->obj->DB_select_all("iweb_wzplist"," `status`='1' ORDER BY $order DESC limit 12");
		$hot = $this->comname($hot,$company);
		return $hot;
	}
}
?>