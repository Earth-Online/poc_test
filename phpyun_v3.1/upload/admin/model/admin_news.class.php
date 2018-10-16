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
class admin_news_controller extends common
{
	function index_action(){
         $where="1";
		if($_GET['news_search'])
		{
			if ($_GET['cate']!=''){
				$where.=" and `nid`='".$_GET['cate']."'";
				$urlarr['cate']=$_GET['cate'];
			}
			if ($_GET['type']=='1'){
				$where.=" and `title` like '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=='2'){
				$where.=" and `author` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
			$urlarr['news_search']=$_GET['news_search'];
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['order']=$_GET['order'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$adminnews=$this->get_page("news_base",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($adminnews)){
			foreach($adminnews as $v){
				$classid[]=$v['nid'];
			}
		}
		if(is_array($classid))
		{
			$group=$this->obj->DB_select_all("news_group","id in (".@implode(",",$classid).")");
		}
		$property=$this->obj->DB_select_all("property");
		if(is_array($group)){
			foreach($adminnews as $k=>$v){
				$adminnews[$k]['url']=$this->config['sy_weburl']."/news/".date("Ymd",$v['datetime'])."/".$v['id'].".html";
				$adminnews[$k]['title']=mb_substr($v['title'],0,20,"GBK");
				foreach($group as $key=>$value){
					if($value['id']==$v['nid']){
						$adminnews[$k]['name']=$value['name'];
					}
				}
				if($v['newsphoto']!=""){
					$type.=" 图";
				}
				if($v['describe']!=""){
					$describe=@explode(",",$v['describe']);
					foreach($property as $val){
						if(in_array($val['value'],$describe)){
							$ms=mb_substr($val['name'],0,1,"GBK");
							$type.=" ".$ms;
						}
					}
				}
				if($type!=""){
					$adminnews[$k]['titype']="[<font color='red'>".$type."</font> ]";
				}
				$type=$describe="";
			}

		}
		$cate=$this->obj->DB_select_all("news_group","1","`id`,`name`");
		$this->yunset("cate", $cate);
        $this->yunset("get_type", $_GET);
		$this->yunset("adminnews",$adminnews);
		$this->yunset("property",$property);
		$this->yunset("propertys",$property);
		$this->yuntpl(array('admin/admin_news_list'));
	}
	function property_action(){
		if(!$_POST['id']){
			$nbid=$this->obj->DB_insert_once("property","`name`='".$_POST['name']."',`value`='".$_POST['value']."'");
			isset($nbid)?$this->obj->ACT_layer_msg( "新闻属性(ID:".$nbid.")添加成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "添加失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$nbid=$this->obj->DB_update_all("property","`name`='".$_POST['name']."',`value`='".$_POST['value']."'","`id`='".$_POST['id']."'");
			isset($nbid)?$this->obj->ACT_layer_msg( "新闻属性(ID:".$_POST['id'].")更新成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "更新失败！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function savepro_action(){
		if($_POST['type']=="add"){
			$describe=@implode(",",$_POST['describe']);
			$row=$this->obj->DB_select_all("news_base","`id` in (".$_POST['proid'].")");
			foreach($row as $k=>$v){
				if($v['describe']==""){
					$this->obj->DB_update_all("news_base","`describe`='".$describe."'","`id`='".$v['id']."'");
				}else{
					$this->obj->DB_update_all("news_base","`describe`=concat(`describe`,',".$describe."')","`id`='".$v['id']."'");
				}
			}
			$this->obj->ACT_layer_msg( "添加成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
		if($_POST['type']=="del"){
			if($_POST['describe']!=""){
				$describe=@implode(",",$_POST['describe']);
			}else{
				$describe="";
			}
			$this->obj->DB_update_all("news_base","`describe`='".$describe."'","`id` in(".$_POST['proid'].")");

			$this->obj->ACT_layer_msg( "删除成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
	}
	function delpro_action(){
		$this->check_token();
		if($_GET['id']){
			$this->obj->DB_delete_all("property","`id`='".(int)$_GET['id']."'");
			$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']);
		}
	}
	function news_action(){
		$news_group=$this->obj->DB_select_all("news_group");
		$a=0;$b=0;
		if(is_array($news_group)){
			foreach($news_group as $key=>$v){
				if($v['keyid']==0){
					$one_class[$a]['id']=$v['id'];
					$one_class[$a]['name']=$v['name'];
					$a++;
				}
				if($v['keyid']!=0){
					if(!is_array($two_class[$v['keyid']]))$b=0;
					$two_class[$v['keyid']][$b]['id']=$v['id'];
					$two_class[$v['keyid']][$b]['name']=$v['name'];
					$b++;
				}
			}
		}
        $this->yunset("one_class",$one_class);
	    $this->yunset("two_class",$two_class);
		$property=$this->obj->DB_select_all("property");
		$this->yunset("property",$property);
		if($_GET['id']){
			$newsgroup = $this->obj->DB_select_once("news_base","`id`='".$_GET['id']."'");
			$row = $this->obj->DB_select_once("news_content","`nbid`='".$_GET['id']."'");
			$newsgroup['content']=str_replace("&","&amp;",$row['content']);
			$describe=@explode(',',$newsgroup['describe']);
			$this->yunset("describe",$describe);
			$this->yunset("news",$newsgroup);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
		$where=1;
		$shell=$this->obj->DB_select_once("admin_user","`uid`='".$_SESSION['auid']."'");
		$where="`id` in (".$shell['domain'].")";
		$shelldomain=@explode(",",$shell['domain']);
		$this->yunset("shelldomain",$shelldomain);
		$domain = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		$this->yunset("domain",$domain);
		$this->yuntpl(array('admin/admin_news_add'));
	}
	function addnews_action()
	{
		if($_POST['updatenews'])
		{
			$news_base=$this->obj->DB_select_once('news_base',"`id`='".$_POST['id']."'","`newsphoto`,`s_thumb`,`datetime`");
			if($_POST['uplocadpic'])
			{
				if($news_base['s_thumb']!=$_POST['uplocadpic']){
					if($news_base['s_thumb']){
						$this->obj->unlink_pic('../'.$news_base['s_thumb']);
						$this->obj->unlink_pic('../'.$news_base['newsphoto']);
					}
					$upload=$this->upload_pic("../upload/news/");
					if(file_exists('..'.$_POST['uplocadpic'])){
						$uplocadpic=$upload->picture('..'.$_POST['uplocadpic'],false,1);//同名文件已经存在了
 						$s_thumb=$upload->news_makeThumb($uplocadpic,120,120,'_S_');
						$value.="`newsphoto`='" . str_replace("../upload/new","upload/new",$uplocadpic) . "',";
						$value.="`s_thumb`='" . str_replace("../upload/new","upload/new",$s_thumb) . "',";
					}else{
						$s_thumb=$upload->news_makeThumb('..'.$_POST['uplocadpic'],120,120,'_S_');
						$value.="`newsphoto`='" . str_replace("/data/","data/",$_POST['uplocadpic']) . "',";
						$value.="`s_thumb`='" . str_replace("../data/","data/",$s_thumb) . "',";
					}
				}
				$this->unlink_pic('..'.$_POST['uplocadpic']);
			}
			$content = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',''),html_entity_decode($_POST["content"],ENT_QUOTES,"GB2312"));
			$describe = @implode(",",$_POST['describe']);
			$time = time();
			$value.="`color`='".$_POST['color']."',";
			$value.="`sort`='".$_POST['sort']."',";
			$value.="`nid`='".$_POST['nid']."',";
			$value.="`did`='".$_POST['did']."',";
			$value.="`author`='".$_POST['author']."',";
			$value.="`source`='".$_POST['source']."',";
			$value.="`title`='".$_POST['title']."',";
			$value.="`describe`='".$describe."',";
			$value.="`lastupdate`='".$time."',";
			if(!$_POST['keyword']){
				require(APP_PATH."/include/lib_splitword_class.php");
				$sp = new SplitWord();
				$keywordarr=$sp->getkeyword(strip_tags($content));
				$value.="`keyword`='".@implode(",",$keywordarr)."',";
			}else{
				$value.="`keyword`='".$_POST['keyword']."',";
			}
			$value.="`description`='".$_POST['description']."'";
			$nbid=$this->obj->DB_update_all("news_base",$value,"`id`='".$_POST['id']."'");

			$row=$this->obj->DB_select_once('news_content',"`nbid`='".$_POST['id']."'","nbid");
			if(is_array($row)){
				$cont = $this->obj->DB_update_all("news_content", "`content`='".$content."'","`nbid`='".$_POST['id']."'");
			}else{
				$cont = $this->obj->DB_insert_once("news_content", "`nbid`='".$_POST['id']."',`content`='".$content."'");
			}
			$this->autohtml($_POST['id'],$news_base['datetime']);
			$lasturl=str_replace("&amp;","&",$_POST['lasturl']);
			isset($nbid)?$this->obj->ACT_layer_msg( "新闻(ID:".$_POST['id'].")更新成功！",9,$lasturl,2,1):$$this->obj->ACT_layer_msg( "更新失败！",8,$lasturl);
		}
	    if($_POST["newsadd"]){
			if($_POST['uplocadpic']){
				$upload=$this->upload_pic("../upload/news/");
				if(file_exists('..'.$_POST['uplocadpic'])){
					$uplocadpic=$upload->picture('..'.$_POST['uplocadpic'],false,1);
 					$s_thumb=$upload->news_makeThumb($uplocadpic,120,120,'_S_');
					$value.="`newsphoto`='" . str_replace("../upload/new","upload/new",$uplocadpic) . "',";
					$value.="`s_thumb`='" . str_replace("../upload/new","upload/new",$s_thumb) . "',";
				}else{
					$s_thumb=$upload->news_makeThumb('..'.$_POST['uplocadpic'],120,120,'_S_');
					$value.="`newsphoto`='" . str_replace("/data/","data/",$_POST['uplocadpic']) . "',";
					$value.="`s_thumb`='" . str_replace("../data/","data/",$s_thumb) . "',";
				}
				$this->unlink_pic('..'.$_POST['uplocadpic']);
			}

			$content = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',""),html_entity_decode($_POST["content"],ENT_QUOTES,"GB2312"));
			$describe = @implode(",",$_POST['describe']);
			$time = time();
			if($_POST['sort']!=''){
				$value.="`sort`='".$_POST['sort']."',";
			}else{
				$value.="`sort`='0',";
			}
			$value.="`color`='".$_POST['color']."',";
			$value.="`nid`='".$_POST['nid']."',";
			$value.="`did`='".$_POST['did']."',";
			$value.="`author`='".$_POST['author']."',";
			$value.="`source`='".$_POST['source']."',";
			$value.="`title`='".$_POST['title']."',";
			$value.="`datetime`='".$time."',";
			$value.="`lastupdate`='".$time."',";
			$value.="`describe`='".$describe."',";

			if(!$_POST["keyword"]){
				require(APP_PATH."/include/lib_splitword_class.php");
				$sp = new SplitWord();
				$keywordarr=$sp->getkeyword(strip_tags($content));
				$value.="`keyword`='".@implode(",",$keywordarr)."',";
			}else{
				$value.="`keyword`='".$_POST['keyword']."',";
			}
			$value.="`description`='".$_POST["description"]."'";
			$nbid=$this->obj->DB_insert_once("news_base",$value);
			$cont = $this->obj->DB_insert_once("news_content", "`nbid`='$nbid',`content`='" .$content. "'");
			$this->autohtml($nbid,$time);
			isset($cont)?$this->obj->ACT_layer_msg( "新闻(ID:".$nbid.")添加成功！",9,"index.php?m=admin_news",2,1):$this->obj->ACT_layer_msg( "添加失败！",8,"index.php?m=admin_news");
		}
	}
	function group_action(){
		$news_group = $this->obj->DB_select_all("news_group","1 order by `id` desc");
		if(is_array($news_group)){
			foreach($news_group as $key=>$value){
				if($value['keyid']!=0){
					$rootid[$value['keyid']][] = $value['id'];
				}else{
					$rootid[$value['id']][] = $value['id'];
				}
			}
		}
		if(is_array($rootid)){
			foreach($rootid as $k=>$v){
				$root_arr = @implode(",",$v);
				$count = $this->obj->DB_select_num("news_base","`nid`='$k' or nid IN ($root_arr)");
				foreach($news_group as $key=>$value){
					if($value['id']==$k){
						$news_group[$key]['count'] = $count;
						$news_group[$key]['roots'] = count($v)-1;
					}
				}
			}
		}
	    $info=$this->obj->DB_select_once("news_group","`id`='".$_GET['id']."'");
	    $subclass=$this->obj->DB_select_all("news_group","`keyid`='".$info['id']."'");
	    $news=$this->obj->DB_select_all("news_group","keyid!=0");

        $this->yunset("info",$info);
        $this->yunset("news_group",$news_group);
        $this->yunset("subclass",$subclass);

		$this->yuntpl(array('admin/admin_news_group'));
	}
	function addgroup_action(){
        if($_POST['sub']){
			if($_POST['classname']!=""){
				if(!is_array($this->obj->DB_select_once("news_group","name='".$_POST['classname']."'"))){
					if($_POST['f_id']=='0'){
						$va="`name`='".$_POST['classname']."',`keyid`='".$_POST['f_id']."',`rec`='".$_POST['rec']."'";
					}else{
						$va="`name`='".$_POST['classname']."',`keyid`='".$_POST['f_id']."'";
					}
					$nbid=$this->obj->DB_insert_once("news_group",$va);
					$this->get_cache();
					isset($nbid)?$this->obj->ACT_layer_msg( "新闻类别(ID:".$nbid.")添加成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "添加失败！",8,$_SERVER['HTTP_REFERER']);
			     }else{
				   $this->obj->ACT_layer_msg( "已经存在此类别！",8,$_SERVER['HTTP_REFERER']);
			    }
			}else{
				$this->obj->ACT_layer_msg( "请正确填写你的类别！",8,$_SERVER['HTTP_REFERER']);
		    }
        }
	    if($_POST['update']){
	    	 $update=$this->obj->DB_update_all("news_group","`name`='".$_POST['procity']."'","`id`='".$_POST['id']."'");
	         $this->get_cache();
			 isset($update)?$this->obj->ACT_layer_msg( "新闻类别(ID:".$_POST['id'].")更新成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "更新失败！",8,$_SERVER['HTTP_REFERER']);
	    }
	}
	function recommend_action(){
		$nid=$this->obj->DB_update_all("news_group","`".$_GET['type']."`='".$_GET['rec']."'","`id`='".$_GET['id']."' and `keyid`='0'");
		$this->get_cache();
		echo $nid?1:0;die;
	}
	function delnews_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if(is_array($del)){
				$this->obj->DB_delete_all("news_base","`id` in(".@implode(',',$del).")","");
				$this->obj->DB_delete_all("news_content","`nbid` in(".@implode(',',$del).")","");
 				$this->layer_msg('新闻(ID:'.@implode(',',$del).')删除成功！',9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg('请选择您要删除的新闻！',8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$where="`id`='".$_GET['id']."'";
			$result=$this->obj->DB_delete_all("news_base", $where);
			$nid=$this->obj->DB_delete_all("news_content","`nbid`='".$_GET['id']."'");
			isset($nid)?$this->layer_msg('新闻(ID:'.$_GET['id'].')删除成功！',9):$this->layer_msg('删除失败！',8);
		}else{
			$this->obj->ACT_layer_msg( "非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}

	function delgroup_action()
	{
		$this->check_token();
	   if(isset($_GET['id']))
	   {
			$result=$this->obj->DB_delete_all("news_group","`id`='".$_GET['id']."' or `keyid`='".$_GET['id']."'","");
			$this->get_cache();
			isset($result)?$this->layer_msg('删除成功！',9):$this->layer_msg('删除失败！',8);
	   }
	}
	function autohtml($id,$time)
	{
		$url=$this->config["sy_weburl"]."/index.php?m=news&c=show&id=".$id.".html";
		$dir=date("Ymd",$time);
		$fw=$this->obj->html("../news/".$dir."/".$id.".html",$url);
	}
	function ajax_action()
	{
		if($_POST['sort'])
		{
			$this->obj->DB_update_all("news_group","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
		}
		if($_POST['name'])
		{
			$_POST['name']=iconv("UTF-8","gbk",$_POST['name']);
			$this->obj->DB_update_all("news_group","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
		}
		$this->get_cache();
		echo '1';die;
	}
	function make_cache_action(){
		$result=$this->get_cache();
		echo $result? 1:0;die;
	}
	function get_cache(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		return $makecache=$cacheclass->group_cache("group.cache.php");
	}
}


?>