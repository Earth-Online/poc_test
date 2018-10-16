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
class hotjob_controller extends common
{
	function index_action(){
		extract($_GET);
		extract($_POST);
		if($type=="add"){
			if($comsearch){
				$where=1;
				if($username!=""){
					$where.=" and name LIKE '%".$username."%'";
					$urlarr['username']=$username;
				}
				$urlarr['comsearch']=$comsearch;
				$urlarr['type']="add";
				$urlarr['page']="{{page}}";
				$pageurl=$this->url("index",$_GET["m"],$urlarr);
				$com =$this->get_page("company",$where,$pageurl,$this->config["sy_listnum"],"`uid`,`name`");
				if(is_array($com)&&$com){
					foreach($com as $val){
						$uids[]=$val['uid'];
					}
					$member=$this->obj->DB_select_all("member","`uid` in(".@implode(',',$uids).")","reg_date,uid");
					$statis =$this->obj->DB_select_all("company_statis","`uid` in(".@implode(',',$uids).")","`uid`,`rating`,`rating_name`");
					foreach($com as $key=>$val){
						foreach($member as $v){
							if($val['uid']==$v['uid']){
								$com[$key]['reg_date']=$v['reg_date'];
							}
						}
						foreach($statis as $value){
							if($val['uid']==$value['uid']){
								$com[$key]["rating"] = $value["rating"];
								$com[$key]["rat_name"] = $value["rating_name"];
							}
						}
					}
				}
				$this->yunset("com",$com);
				$this->yunset("comsearch",$comsearch);
			}
			$this->yunset("type",$type);
		}else{
			$where=1;
			if($_GET['keyword']){
				$where.=" and `username` like '%".$_GET['keyword']."%'";
				$urlarr['keyword']=$_GET['keyword'];
			}
			$comwhere=1;
			$com=$this->obj->DB_select_all("company",$comwhere,"`uid`");
			if(is_array($com)){
				foreach($com as $v){
					$uid[]=$v['uid'];
				}
				$uid=@implode(",",$uid);
				$where.=" and `uid` in (".$uid.")";
			}
			if($_GET['order']){
				$where.=" order by ".$_GET['t']." ".$_GET['order'];
				$urlarr['order']=$_GET['order'];
				$urlarr['t']=$_GET['t'];
			}else{
				$where.=" order by id desc";
			}
			$urlarr['page']="{{page}}";
			$pageurl=$this->url("index",$_GET["m"],$urlarr);
			$hotjob =$this->get_page("hotjob",$where,$pageurl,$this->config["sy_listnum"]);
			$this->yunset("hotjob",$hotjob);
		}
		$this->yuntpl(array('admin/admin_hotjob_list'));

	}
	function hotjobinfo_action(){
		if($_GET['id']){
			$hotjob=$this->obj->DB_select_once("hotjob","`id`='".(int)$_GET['id']."'");
		}else if($_GET['uid']){
			$row = $this->obj->DB_select_alls("company","company_statis","a.`uid`='".(int)$_GET['uid']."' and b.`uid`='".(int)$_GET['uid']."'","a.`content`,a.`name` as username,b.`rating_name` as rating,a.`uid`");
			$row=$row[0];
			$row['content']=@explode(' ',trim(strip_tags($row['content'])));
			if(is_array($row['content'])&&$row['content']){
				foreach($row['content'] as $val){
					$row['beizhu'].=trim($val);
				}
			}else{
				$row['beizhu']=$row['content'];
			}
			$hotjob=$row;
			$hotjob['time_start']=time();
		}
		$this->yunset("hotjob",$hotjob);
		$this->yuntpl(array('admin/admin_hotjob_info'));
	}
	function add_action()
	{
		extract($_GET);
		if($id)
		{
			$row = $this->obj->DB_select_once("hotjob","`id`='$id'");
			$this->yunset("row",$row);
		}
		$this->yunset("username",$username);
		$this->yunset("rating",$rating);
		$this->yunset("uid",$uid);
		$this->yuntpl(array('admin/admin_hotjob_add'));
	}
	function save_action(){
		extract($_POST);
		if(is_uploaded_file($_FILES['hot_pic']['tmp_name'])){
			$upload=$this->upload_pic("../upload/hotpic/");
			$pictures=$upload->picture($_FILES['hot_pic']);
			$pic = str_replace("../upload","upload",$pictures);
		}else{
			if($_POST["hotad"]){
				$this->obj->ACT_layer_msg("企业展示LOGO不能为空！",8,"index.php?m=hotjob",2,1);
			}
		}
		if($_POST["hotad"]){
			$start = strtotime($time_start);
			$end = strtotime($time_end);
			$nid=$this->obj->DB_insert_once("hotjob","`uid`='$uid',`username`='$username',`sort`='$sort',`rating`='$rating',`hot_pic`='$pic',`service_price`='$service_price',`beizhu`='$beizhu',`time_start`='$start',`time_end`='$end'");
			$this->obj->ACT_layer_msg("招聘(ID:".$nid.")设定成功！",9,"index.php?m=hotjob",2,1);
		}elseif($_POST["hotup"]){
			$start = strtotime($time_start);
			$end = strtotime($time_end);
			$value = "`service_price`='$service_price',`time_start`='$start',`time_end`='$end',`beizhu`='$beizhu',`sort`='$sort'";
			if($pic!=""){
				$hot=$this->obj->DB_select_once("hotjob","`id`='$id'");
				$this->obj->unlink_pic("../".$hot["hot_pic"]);
				$value.=",`hot_pic`='$pic'";
			}
			$this->obj->DB_update_all("hotjob",$value,"`id`='$id'");
			$this->obj->ACT_layer_msg("名企招聘(ID:".$id.")修改成功！",9,"index.php?m=hotjob",2,1);
		}
		$this->yuntpl(array('admin/admin_hotjob_add'));
	}

	function del_action(){
		$this->check_token();
	    if(isset($_GET["id"])){
	    	$hot=$this->obj->DB_select_once("hotjob","`id`='".$_GET["id"]."'");
			$this->obj->unlink_pic("../".$hot["hot_pic"]);
			$result=$this->obj->DB_delete_all("hotjob","`id`='".$_GET["id"]."'" );
			isset($result)?$this->layer_msg('名企招聘(ID:'.$_GET["id"].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
}




?>