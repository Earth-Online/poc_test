<?php
/*
 * 单个人才调用
 * ----------------------------------------------------------------------------
 * 分别对 职位，对应招聘公司，公司所属企业级别，认证等级等作操作
 *
 * ============================================================================
*/
function smarty_function_history($paramer,&$smarty){
	global $db,$db_config,$config;
	if($paramer[type]==1){
		$_SESSION[history][$paramer[jobid]] =$paramer;
    }
    if($paramer[type]==2){
    	if($paramer[uid]!=""){
    		$arr=$db->DB_select_once("look_resume","`com_id`='$paramer[uid]' and `resume_id`='$paramer[id]'");
			if(!is_array($arr)){
				$db->insert_once("look_resume","`uid`='$paramer[user_id]',`com_id`='$paramer[uid]',`resume_id`='$paramer[id]',`datetime`='".mktime()."'");
			}else{
				$db->update_all("look_resume","`datetime`='".mktime()."'","`id`='$arr[id]'");
			}
    	}
    }
}