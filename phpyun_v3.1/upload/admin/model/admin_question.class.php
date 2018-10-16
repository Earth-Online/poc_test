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
class admin_question_controller extends common{
	function index_action(){
		$where=1;
		if($_GET['id'])
		{
			$where.=" and `id`='".$_GET['id']."'";
			$urlarr['id']=$_GET['id'];
		}
		if($_GET['news_search']){
			if ($_GET['type']=='1'){
				$where.=" and `title` like '%".$_GET['name']."%' ";
			}elseif ($_GET['type']=='2'){
				$queinfo=$this->obj->DB_select_all("member","`username` like '%".$_GET['name']."%'","`uid`");
				if (is_array($queinfo)){
					foreach ($queinfo as $k=>$v){
						$queuids[]=$v['uid'];
					}
					$uids=@implode(",",$queuids);
				}
				$where.=" and `uid` in (".$uids.")";
			}
			$urlarr['name']=$_GET['name'];
			$urlarr['type']=$_GET['type'];
            $urlarr['news_search']=$_GET['news_search'];
			$this->yunset("name",$_GET['name']);
		}
		if ($_GET['is_recom']=='0'){
			$where.=" and `is_recom`='0'";
		}elseif($_GET['is_recom']=='1'){
			$where.=" and `is_recom`='1'";
		}
		$wheres=1;
		if($_SESSION['admin_city']){
			$wheres.=" and `cityid`='".$_SESSION['admin_city']."'";
		}
		if($_SESSION['admin_hy']){
			$wheres.=" and `hy`='".$_SESSION['admin_hy']."'";
		}
		if($_SESSION['def_city'] || $_SESSION['def_hy'])
		{
			if($_SESSION['def_city']){
				$session[]="`cityid` in (".$_SESSION['def_city'].")";
			}
			if($_SESSION['def_hy']){
				$session[]="`hy` in (".$_SESSION['def_hy'].")";
			}
			$wheres.=" and (".@implode(" or ",$session).")";
		}
		$user=$this->obj->DB_select_all("member","`usertype`='1'","uid");
		$com=$this->obj->DB_select_all("company",$wheres,"`uid`");
		if(is_array($user))
		{
			foreach($user as $v)
			{
				$uid[]=$v['uid'];
			}
		}
		if(is_array($com))
		{
			foreach($com as $v)
			{
				$uid[]=$v['uid'];
			}
		}
		$where.=" AND `uid` in (".@implode(",",$uid).")";
		if($_GET['order']){
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$question=$this->get_page("question",$where,$pageurl,$this->config['sy_listnum']);
		$m_id=array();
		$c_id=array();
		foreach($question as $q_k=>$q_v){
			if(in_array($q_v['uid'],$m_id)==false){
				$m_id[]=$q_v['uid'];
			}
			if(in_array($q_v['cid'],$c_id)==false){
				$c_id[]=$q_v['cid'];
			}
		}
		$m_ids=@implode(',',$m_id);
		$c_ids=@implode(',',$c_id);
		$member=$this->obj->DB_select_all("member","`uid` in(".$m_ids.")","`uid`,`username`");

		$q_class=$this->obj->DB_select_all("q_class","`id` in(".$c_ids.")","`id`,`name`");
		foreach($question as $key=>$val){
			foreach($member as $m_v){
				if($val['uid']==$m_v['uid']){
					$question[$key]['username']=$m_v['username'];
				}
			}
			foreach($q_class as $q_v){
				if($val['cid']==$q_v['id']){
					$question[$key]['classname']=$q_v['name'];
				}
			}
		}
        $this->yunset("get_type", $_GET);
		$this->yunset("question",$question);
		$this->yuntpl(array('admin/admin_question_list'));
	}


	function add_action(){

		if($_GET['id']){
			$question_show=$this->obj->DB_select_once("question","id='".$_GET['id']."'");
			$this->yunset("question_show",$question_show);
		}
		$all_class = $this->obj->DB_select_all("q_class","1 order by sort","`id`,`name`,`pid`");
		foreach($all_class as $a_k=>$a_v){
			if($a_v['id']==$question_show['cid']){
				$pid=$a_v['pid'];
				$all_class[$a_k]['is_select']='1';

			}
			if($a_v['pid']=='0'){
				$p_class[]=$a_v;
			}
		}
		$this->yunset("class_list",$p_class);
		$this->yunset("pid",$pid);
		$s_class = $this->obj->DB_select_all("q_class","`pid`='".$pid."' order by sort","`id`,`name`,`pid`");
		$this->yunset("s_class",$s_class);
		$this->yuntpl(array('admin/admin_question_add'));
	}

	function get_class_action(){
		if($_POST['pid']){
			$q_class=$this->obj->DB_select_all("q_class","pid='".$_POST['pid']."' order by `sort`","`id`,`name`,`pid`");
			if($q_class[0]){
				$html='';
				foreach($q_class as $q_v){
					$html.="<option value='".$q_v['id']."'>".$q_v['name']."</option>";
				}
				echo $html;
			}else{
				echo $html="<option value='0'>该分类下暂无子类！</option>";
			}

		}
	}

	function save_action(){
		if($_POST['update']){
			$value.="`title`='".$_POST['title']."',";
			$value.="`cid`='".$_POST['cid']."',";
			$value.="`visit`='".$_POST['visit']."',";
			$value.="`is_recom`='".$_POST['is_recom']."',";
			$content = str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$value.="`content`='".$content."'";
			$nbid=$this->obj->DB_update_all("question",$value,"`id`='".$_POST['id']."'");
			isset($nbid)?$this->obj->ACT_layer_msg("问答(ID:".$_POST['id'].")更新成功！",9,"index.php?m=admin_question",2,1):$this->obj->ACT_layer_msg("更新失败！",8,"index.php?m=admin_question");
		}
	}

	function del_action(){
		$this->check_token();
		if($_GET['del']){

	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
					$ids=@implode(',',$del);
					$this->obj->DB_delete_all("question","`id` in(".$ids.")","");
					$this->obj->DB_delete_all("answer","`qid` in(".$ids.")","");
					$this->obj->DB_delete_all("answer_review","`qid` in(".$ids.")","");
			    }else{
					$ids=$del;
	    		 	$this->obj->DB_delete_all("question","`id`='$del'");
					$this->obj->DB_delete_all("answer","`qid`='".$del."'","");
					$this->obj->DB_delete_all("answer_review","`qid`='".$del."'","");
	    		}
	    		$this->layer_msg( "问答(ID:".$ids.")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("answer","`qid`='".$_GET['id']."'","");
			$this->obj->DB_delete_all("answer_review","`qid`='".$_GET['id']."'","");
			$this->obj->DB_delete_all("question","`id`='".$_GET['id']."'");
			isset($result)?$this->layer_msg('问答(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('非法操作！',3,0,$_SERVER['HTTP_REFERER']);
		}
	}

	function get_answer_action(){
		if($_GET['aid'])
		{
			$where="`id`='".$_GET['aid']."'";
		}else{
			$where="`qid`='".$_GET['id']."'";
		}
		$all_answer = $this->obj->DB_select_all("answer",$where." order by add_time desc");
		foreach($all_answer as $a_v){
			$a_uid[]=$a_v['uid'];
		}
		$a_ids=@implode(',',$a_uid);
		$a_member=$this->obj->DB_select_all("member","`uid` in(".$a_ids.")","`uid`,`username`");
		foreach($all_answer as $key=>$val){
			foreach($a_member as $m_u){
				if($val['uid']==$m_u['uid']){
					$all_answer[$key]['username']=$m_u['username'];
				}
			}
		}
		$this->yunset("qid",$_GET['id']);
		$this->yunset("all_answer",$all_answer);
		$this->yuntpl(array('admin/admin_answer_list'));
	}


	function add_answer_action(){
		if($_GET['id']){
			$answer=$this->obj->DB_select_alls("question","answer","b.`id`='".$_GET['id']."' and b.`qid`=a.`id`");
			$this->yunset("answer",$answer[0]);
		}
		if($_GET['qid']){
			$answer=$this->obj->DB_select_once("question","`id`='".$_GET['qid']."'","title,id as qid");
			$this->yunset("answer",$answer);
		}
		if($_GET['back_url']){
			$this->yunset("back_url",$_GET['back_url']);
		}
		$this->yuntpl(array('admin/admin_add_answer'));
	}

	function save_answer_action(){
		$url="index.php?m=admin_question&c=get_answer&id=".$_POST['qid'];
		if($_POST['update']){
			$value.="`oppose`='".$_POST['oppose']."',";
			$value.="`support`='".$_POST['support']."',";
			$content = str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$value.="`content`='".$content."'";
			$a_id=$this->obj->DB_update_all("answer",$value,"`id`='".$_POST['id']."'");
			$a_id?$this->obj->ACT_layer_msg("回答(ID:".$_POST['id'].")更新成功！",9,$url,2,1):$this->obj->ACT_layer_msg("更新失败！",8,$url);
		}
	}

	function del_answer_action(){

		$this->check_token();
		if($_GET['del']){
	    	$a_del=$_GET['del'];
	    	if($a_del){
	    		if(is_array($a_del)){
					$a_ids=@implode(',',$a_del);
					$this->obj->DB_delete_all("answer", "`id` in(".$a_ids.")","");
					$this->obj->DB_delete_all("answer_review","`aid` in(".$a_ids.")","");
			    }else{
					$a_ids=$a_del;
	    		 	$this->obj->DB_delete_all("answer","`id`='$a_del'");
					$this->obj->DB_delete_all("answer_review","`aid`='".$a_del."'","");
	    		}
	    		$this->obj->DB_update_all("question","`answer_num`=`answer_num`-".count($a_del),"`id`='".$_GET['qid']."'");
	    		$this->layer_msg( "用户回答(".$a_ids.")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET["id"])){
			$this->obj->DB_delete_all("answer_review","`aid`='".$_GET['id']."'","");
			$result=$this->obj->DB_delete_all("answer", "`id`='".$_GET['id']."'");
			$this->obj->DB_update_all("question","`answer_num`=`answer_num`-1","`id`='".$_GET['qid']."'");
			isset($result)?$this->layer_msg('用户回答('.$_GET['qid'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('非法操作！',3,0,$_SERVER['HTTP_REFERER']);
		}
	}

	function get_comment_action(){
		if($_GET['aid'])
		{
			$where="b.`id`='".$_GET['aid']."'";
		}else{
			$where="b.`aid`='".$_GET['id']."'";
		}
		$answer_review=$this->obj->DB_select_alls("member","answer_review",$where." and b.`uid`=a.`uid`","b.`content`,b.`id`,a.`username`,b.`add_time`,b.`qid`");
		$this->yunset("answer_review",$answer_review);
		$this->yuntpl(array('admin/admin_answer_review'));
	}

	function add_review_action(){
		if($_GET['id']){
			$review_show=$this->obj->DB_select_once("answer_review","`id`='".$_GET['id']."'");
			$this->yunset("review_show",$review_show);
		}else{
			$this->yunset("aid",$_GET['aid']);
			$this->yunset("qid",$_GET['qid']);
		}
		$this->yuntpl(array('admin/admin_add_review'));
	}

	function save_review_action(){
		$url="index.php?m=admin_question&c=get_comment&id=".$_POST['aid'];
		if($_POST['update']){
			$review.="`content`='".$_POST['content']."'";
			$r_id=$this->obj->DB_update_all("answer_review",$review,"`id`='".$_POST['id']."'");
			isset($r_id)?$this->obj->ACT_layer_msg("问答评论(ID:".$_POST['id'].")更新成功！",9,$url,2,1):$this->obj->ACT_layer_msg("更新失败！",8,$url);
		}
	}
	function del_review_action(){
		$this->check_token();
		if($_GET['del']){

	    	$r_del=$_GET['del'];
	    	if($r_del){
	    		if(is_array($r_del)){
					$r_ids=@implode(',',$r_del);
					$this->obj->DB_delete_all("answer_review", "`id` in(".$r_ids.")","");
			    }else{
					$r_ids=$r_del;
	    		 	$this->obj->DB_delete_all("answer_review","`id`='$r_del'");
	    		}
	    		$this->obj->DB_update_all("answer","`comment`=`comment`-".count($r_del),"`id`='".$_GET['aid']."'");
	    		$this->layer_msg( "问答评论(ID:".$r_ids.")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET["id"])){
			$result=$this->obj->DB_delete_all("answer_review", "`id`='".$_GET['id']."'");
			$this->obj->DB_delete_all("report", "`eid`='".$_GET['id']."' and `r_type`='3' and `type`='1'","");
			$this->obj->DB_update_all("answer","`comment`=`comment`-1","`id`='".$_GET['aid']."'");
			isset($result)?$this->layer_msg('问答评论(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('非法操作！',3,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>