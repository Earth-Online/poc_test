<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2017 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class topic_controller extends ask_controller{  
	function index_action(){
		$M=$this->MODEL('ask');
		$recom=$M->GetQuestionList(array('is_recom'=>'1'),array('field'=>'uid,nickname,pic','limit'=>'8','orderby'=>'add_time'));
		foreach($recom as $k=>$v){
			if($v['pic']){
				$recom[$k]['pic']=$this->config['sy_weburl'].'/'.$v['pic'];
			}else{
				$recom[$k]['pic']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
			}
		}
		if($_GET['pid']){
			$CacheM=$this->MODEL('cache');
			$CacheList=$CacheM->GetCache(array('ask'));
			$data['ask_class_name']=$CacheList['ask_name'][$_GET['pid']];
			$data['ask_class_name']=strip_tags($CacheList['ask_intro'][$_GET['pid']]);
		
		}else{
			$data['ask_class_name']='';
		}
		$this->yunset('recom',$recom);
		$this->yunset("navtype","topic");
		$this->seo("ask_topic");
		$this->ask_tpl('topic');
	}
}
?>