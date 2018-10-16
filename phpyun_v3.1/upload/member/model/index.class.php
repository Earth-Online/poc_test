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
class index_controller extends common{
	function public_action(){
		$user=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`photo`,`resume_photo`,`idcard_pic`,`idcard_status`");
		$this->yunset("user_photo",$user['photo']);
		$this->yunset("resume_photo",$user['resume_photo']);
		$this->yunset("idcard_pic",$user['idcard_pic']);
		$this->yunset("idcard_status",$user['idcard_status']);
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("now_url",$now_url);
	}
	function member_satic(){
		$statis=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
		$this->yunset("statis",$statis);
		return $statis;
	}
	function get_user(){
		$statis=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		if(!$statis['name'] || !$statis['edu']){
			$this->obj->ACT_msg("index.php?c=info","请先完善个人资料！");
		}
	}
	function user_tpl($tpl){
		$this->yuntpl(array('member/user/'.$tpl));
	}
	function logout_action(){
		$this->logout();
	}
	function index_action()
	{
		$this->public_action();
		$this->member_satic();
        $this->com_cache();
		$resume = $this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		$expect=$this->obj->DB_select_once("resume_expect","`id`='".$resume['def_job']."'");
		if($_GET['type']=="job")
		{
			$where="`job_post` in (".$expect['job_classid'].") and `status`<>'1' and `state`='1' and `sdate`<'".mktime()."' and `r_status`<>'2' and `edate`>'".mktime()."'";
		}elseif($_GET['type']=="city"){
			$where="`cityid`='".$expect['cityid']."' and `status`<>'1' and `state`='1' and `sdate`<'".mktime()."' and `r_status`<>'2' and `edate`>'".mktime()."'";
		}else{
			$where="`state`='1' and status<>'1' and `sdate`<'".mktime()."' and `r_status`<>'2' and `edate`>'".mktime()."'";
		}
		$rows=$this->obj->DB_select_all("company_job",$where." order by id desc limit 12","`name`,`id`,`salary`,`edu`,`edate`");
		$newmsg=$this->obj->DB_select_num("userid_msg","`uid`='".$this->uid."' and type<>'1' and `is_browse`='1'");
		$msgnum=$this->obj->DB_select_num("userid_msg","`uid`='".$this->uid."' and type<>'1'");
		$msg_count=$this->obj->DB_select_num("message","`fa_uid`='".$this->uid."' and `username`='管理员'");
		$lookNum=$this->obj->DB_select_num("look_resume","`uid`='".$this->uid."'");
		$downNum=$this->obj->DB_select_num("down_resume","`uid`='".$this->uid."'");
		if($expect['integrity']>0)
		{
			$numresume=$expect['integrity'];
		}else{
			$numresume=100;
		}
		$this->yunset("numresume",$numresume);
		$this->yunset("newmsg", $newmsg);
		$this->yunset("msgnum", $msgnum);
		$this->yunset("msg_count",$msg_count);
		$this->yunset("rows",$rows);
		$this->yunset("resume",$resume);
		$this->yunset("lookNum",$lookNum);
		$this->yunset("downNum",$downNum);
		$this->yunset("js_def",1);
		$this->user_tpl('index');
	}
	function msg_action(){
		if($_GET['del']){
			$del=(int)$_GET['del'];
			$nid=$this->obj->DB_delete_all("userid_msg","`id`='".$del."' and `uid`='".$this->uid."'");
			$nid?$this->layer_msg('删除成功！',9,0,"index.php?c=msg"):$this->layer_msg('删除失败！',8,0,"index.php?c=msg");
		}
		$this->public_action();
		$urlarr=array("c"=>"msg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("userid_msg","`uid`='".$this->uid."' and `type`<>'1' order by id desc",$pageurl,"20");
		if($_GET['c_uid'])
		{
			$data['c_uid']=(int)$_GET['c_uid'];
			$data['inputtime']=mktime();
			$data['p_uid']=$this->uid;
			$data['usertype']=(int)$_COOKIE['usertype'];
			$data['com_name']=$_GET['c_name'];
			$haves=$this->obj->DB_select_once("blacklist","`p_uid`='".$data['p_uid']."' and `c_uid`='".$data['c_uid']."'  and `usertype`='".$data['usertype']."'");
			if(is_array($haves))
			{
				$this->obj->ACT_layer_msg("该用户已在您黑名单中！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->insert_into("blacklist",$data);
				$this->obj->DB_delete_all("userid_msg","`uid`='".$data['p_uid']."' and `fid`='".$data['c_uid']."'"," ");
 				$nid?$this->layer_msg('操作成功！',9,0,"index.php?c=msg"):$this->layer_msg('操作失败！',8,0,"index.php?c=msg");
			}
		}
		$this->yunset("js_def",4);
		$this->user_tpl('msg');
	}
	function up_msg_action()
	{
		$id=(int)$_POST['id'];
		$u_id=$this->obj->update_once('userid_msg',array('is_browse'=>'2'),array('id'=>$id,'uid'=>$this->uid));
		$this->unset_remind("userid_msg","1");
		if($u_id){
			echo 1;die;
		}else{
			echo 0;die;
		}
	}
	function blacklist_action()
	{
		if($_GET['del'])
		{
			$del=(int)$_GET['del'];
			$nid=$this->obj->DB_delete_all("blacklist","`id`='".$del."' and `p_uid`='".$this->uid."'");
			$nid?$this->layer_msg('删除成功！',9,0,"index.php?c=blacklist"):$this->layer_msg('删除失败！',8,0,"index.php?c=blacklist");
 		}
		$this->public_action();
		$urlarr=array("c"=>"blacklist","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("blacklist","`p_uid`='".$this->uid."' and usertype='1' order by id desc",$pageurl,"20");
		$this->yunset("js_def",3);
		$this->user_tpl('blacklist');
	}
	function commsg_action()
	{
		$this->public_action();
		$urlarr=array("c"=>"commsg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("msg","`uid`='".$this->uid."' order by id desc",$pageurl,"20");
		$this->obj->DB_update_all("msg","`user_remind_status`='1'","`uid`='".$this->uid."' and `user_remind_status`='0'");
		$this->unset_remind("usermsg",'1');
		$this->user_tpl('commsg');
	}
	function info_action(){
		if($_POST["submitBtn"]){
			$_POST=$this->post_trim($_POST);
			$is_exist=$this->obj->DB_select_once("member","`uid`!='".$this->uid."' and (`email`='".$_POST['email']."' or `moblie`='".$_POST['telphone']."')","`uid`");
			if($is_exist['uid']){
				$this->obj->ACT_layer_msg("手机或邮箱已存在！",2,$_SERVER['HTTP_REFERER']);
			}
			if($_POST['name']==""){
				$this->obj->ACT_layer_msg("姓名不能为空！",2,$_SERVER['HTTP_REFERER']);
			}
			if($_POST['sex']==""){
				$this->obj->ACT_layer_msg("性别不能为空！",2,$_SERVER['HTTP_REFERER']);
			}
			if($this->config['user_idcard']=="1"&&trim($_POST['idcard'])==""){
				$this->obj->ACT_layer_msg("身份证号码不能为空！",2,$_SERVER['HTTP_REFERER']);
			}
			if($_POST['living']==""){
				$this->obj->ACT_layer_msg("现居住地不能为空！",2,$_SERVER['HTTP_REFERER']);
			}
			unset($_POST['submitBtn']);
			$this->obj->delfiledir("../upload/tel/".$this->uid);
			$where['uid']=$this->uid;
			$nid=$this->obj->update_once("resume",$_POST,$where);
			$this->obj->update_once("member",array('email'=>$_POST['email'],'moblie'=>$_POST['telphone']),$where);
			$member_statis=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'","`resume_num`");
			if($member_statis['resume_num']<1){
				$url="index.php?c=expect&add=".$this->uid;
			}else{
				$url=$_SERVER['HTTP_REFERER'];
			}
			$nid?$this->obj->ACT_layer_msg("信息更新成功！",9,$url):$this->obj->ACT_layer_msg("信息更新失败！",9,$url);
		}
		$this->public_action();
		$row=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		$this->yunset("row",$row);
		$this->city_cache();
		$this->user_cache();
		$this->user_tpl('info');
	}
	function uppic_action(){
		if($_FILES['Filedata']){
			$upload=$this->upload_pic("../upload/user/",false,$this->config['user_pickb']);
			$picture=$upload->picture($_FILES['Filedata']);
			$pictures = @explode("/",$picture);
			echo $pic_ids = end($pictures);
			echo '<script type="text/javascript">window.parent.hideLoading();window.parent.buildAvatarEditor("'.$pic_ids.'","'.$picture.'","photo");</script>';
		}
		$this->public_action();
		$this->user_tpl('uppic');
	}
	function save_avatar_action(){
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		$type = isset($_GET['type'])?trim($_GET['type']):'small';
		$pic_id = trim($_GET['photoId']);
		$nameArr=@explode(".",$pic_id);
		$uptypes=array('jpg','png','jpeg','bmp','gif');
		if(count($nameArr)!=2){
			exit();
		}
		if(!is_numeric($nameArr[0])){
			exit();
		}
		if(!in_array(strtolower($nameArr[1]),$uptypes)){
			$d['statusText'] = iconv("gbk","utf-8",'文件类型不符!');
			$msg = json_encode($d);
			echo $msg;die;
		}
		$new_avatar_path = 'upload/user/user_'.$type.'/'.$pic_id;
		$len = file_put_contents(APP_PATH.$new_avatar_path,file_get_contents("php://input"));
		$avtar_img = imagecreatefromjpeg(APP_PATH.$new_avatar_path);
		imagejpeg($avtar_img,APP_PATH.$new_avatar_path,80);
		$d['data']['urls'][0] ="../".$new_avatar_path;
		$d['status'] = 1;
		$d['statusText'] = iconv("gbk","utf-8",'上传成功!');
		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`photo`,`resume_photo`");
		if($type=="small"){
			$this->obj->update_once('resume',array('photo'=>'./'.$new_avatar_path),array('uid'=>$this->uid));

			$this->obj->unlink_pic('.'.$resume['photo']);
		}else{

			$this->obj->update_once('resume',array('resume_photo'=>'./'.$new_avatar_path),array('uid'=>$this->uid));

			$this->obj->unlink_pic('.'.$resume['resume_photo']);
		}
		$msg = json_encode($d);
		echo $msg;
	}
	function checkimg_action()
	{
		if($_POST["subuppic"])
		{
			$upload=$this->upload_pic("../upload/user/",false,$this->config['user_pickb']);
			$pictures=$upload->picture($_FILES['file']);
			$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
			if($pictures)
			{
				list($width, $height, $type, $attr) = getimagesize($pictures);
				$f1="<img src='$pictures' id='ImageDrag'>";
				$f2="<img src='$pictures' id='ImageIcon'>";
				echo '<script language="javascript">parent.$("#ImageDragContainer").html("'.$f1.'");parent.$("#IconContainer").html("'.$f2.'");parent.$("#bigImage").val("'.$pictures.'");parent.run('.$width.','.$height.');</script>';
				echo "<script>location.href='index.php?m=index&c=checkimg'</script>";exit;
			}else{
				echo "<script>alert('上传文件失败');</script>";
				echo "<script>location.href=''</script>";exit;
			}
		}
		$this->user_tpl('checkimg');
	}
	function job_action(){
		$fnum=$this->obj->DB_select_num("userid_job","`uid`='".$this->uid."'","`id`");
		if($_GET['del']){
			$del=(int)$_GET['del'];
			$nid=$this->obj->DB_delete_all("userid_job","`id`='".$del."' and `uid`='".$this->uid."'");
			if($nid){
				if($fnum>1){$num=$fnum-1;}else{$num=0;}
				$this->obj->update_once('member_statis',array('sq_jobnum'=>$num),array('uid'=>$this->uid));

				$this->layer_msg('删除成功！',9,0,"index.php?c=job");
			}else{
				$this->layer_msg('删除失败！',8,0,"index.php?c=job");
			}
		}
		$this->public_action();
		$this->member_satic();
		$urlarr=array("c"=>"job","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("userid_job","`uid`='".$this->uid."' order by id desc",$pageurl,"20");
		$this->yunset("fnum",$fnum);
		$this->yunset("js_def",3);
		$this->user_tpl('job');
	}
	function favorite_action(){
		$fnum=$this->obj->DB_select_num("fav_job","`uid`='".$this->uid."'","`id`");
		if($_GET['del']){
			$del=(int)$_GET['del'];
			$nid=$this->obj->DB_delete_all("fav_job","`id`='".$del."' and `uid`='".$this->uid."'");
			if($nid){
				if($fnum>1){$num=$fnum-1;}else{$num=0;}
				$this->obj->update_once('member_statis',array('fav_jobnum'=>$num),array('uid'=>$this->uid));

				$this->layer_msg('删除成功！',9,0,"index.php?c=favorite");
			}else{
				$this->layer_msg('删除失败！',8,0,"index.php?c=favorite");
			}
		}
		$this->public_action();
		$this->member_satic();
		$urlarr=array("c"=>"favorite","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("fav_job","`uid`='".$this->uid."' order by id desc",$pageurl,"20");
		$this->yunset("fnum",$fnum);
		$this->yunset("js_def",3);
		$this->user_tpl('favorite');
	}
	function resume_action(){
		if ($_GET['del'] && $_GET['c']=='resume'){
			$del=(int)$_GET['del'];
			$del_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume");
			if($this->obj->DB_delete_all("resume_expect","`id`='".$del."' and `uid`='".$this->uid."'")){
				foreach($del_array as $v){
					$this->obj->DB_delete_all($v,"`eid`='".$del."'' and `uid`='".$this->uid."'","");
				}
				$def_id=$this->obj->DB_select_once("resume","`uid`='".$this->uid."' and `def_job`='".$del."'");
			    if(is_array($def_id)){
					$row=$this->obj->DB_select_once("resume_expect","`uid`='".$this->uid."'");
					$this->obj->update_once('resume',array('def_job'=>$row['id']),array('uid'=>$this->uid));
			    }
				$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`-1","`uid`='".$this->uid."'");
				$this->layer_msg('删除成功！',9,0,"index.php?c=resume");
			}else{
				$this->layer_msg('删除失败！',8,0,"index.php?c=resume");
			}
		}

		if($_GET['update'] && $_GET['c']=='resume'){
			$id=(int)$_GET['update'];
			$nid=$this->obj->update_once('resume_expect',array('lastupdate'=>time()),array('id'=>$id,'uid'=>$this->uid));

 			$nid?$this->layer_msg('刷新成功！',9,0,"index.php?c=resume"):$this->layer_msg('刷新失败！',8,0,"index.php?c=resume");
		}
		$this->public_action();
		$num=$this->member_satic();
		$maxnum=$this->config['user_number']-$num['resume_num'];
		$this->yunset("maxnum",$maxnum);
		$this->yunset("confignum",$this->config['user_number']);
		$rows=$this->obj->DB_select_all("resume_expect","`uid`='".$this->uid."'","id,name,lastupdate,doc");
		$this->yunset("rows",$rows);

		$row=$this->obj->DB_select_once("resume","`idcard_pic`<>'' and `uid`='".$this->uid."'");
		$isallow_addresume="0";
		if($this->config['user_enforce_identitycert']=="1"){
			if($row['idcard_status']=="1"){
				$isallow_addresume="1";
			}else{
				$isallow_addresume="0";
			}
		}else{
			$isallow_addresume="1";
		}
		$this->yunset("isallow_addresume",$isallow_addresume);

		$def_job=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","def_job");
		$this->yunset("def_job",$def_job);
		$this->yunset("js_def",2);
		$this->user_tpl('resume');
	}
	function passwd_action() {
		$this->public_action();
		if($_POST["submit"]) {
			$info = $this->obj->DB_select_once("member","`uid`='".$this->uid."'");
			if(is_array($info))
			{
				$oldpass = md5(md5($_POST['oldpassword']).$info['salt']);
				if($info["password"]!=$oldpass)
				{
					$this->obj->ACT_layer_msg("原始密码错误！", 8,"index.php?c=passwd");
				}
				if($this->config["sy_uc_type"]=="uc_center" &&$info["name_repeat"]!="1")
				{
					$this->obj->uc_open();
					$ucresult= uc_user_edit($info["username"], $_POST['oldpassword'], $_POST['password'], "","1");
					if($ucresult == -1)
					{
						$this->obj->ACT_layer_msg("原始密码错误！", 8,"index.php?c=passwd");
					}
				}else{
					$salt = substr(uniqid(rand()), -6);
					$pass2 = md5(md5($_POST['password']).$salt);
					$this->obj->update_once('member',array('password'=>$pass2,'salt'=>$salt),array('uid'=>$this->uid));

				}
				$this->unset_cookie();
				$this->obj->ACT_layer_msg("修改成功，请重新登录！", 9,$this->config["sy_weburl"]."/index.php?m=login&usertype=".$_POST['usertype']);
			}
		}
		$this->user_tpl('passwd');
	}
	function message_action()
	{
		if($_POST["add_message"])
		{
			$data["content"]=$_POST["content"];
			$data["fa_uid"]=$this->uid;
			$data["username"]=$this->username;
			$data["ctime"]=mktime();
			$nid=$this->obj->insert_into("message",$data);
			$nid?$this->obj->ACT_layer_msg("留言发送成功！",9,"index.php?c=seemessage"):$this->obj->ACT_layer_msg("留言发送失败！",8,"index.php?c=seemessage");
		}
		$this->public_action();
		$this->user_tpl('message');
	}
	function seemessage_action(){
		if($_GET['del']){
			$del=(int)$_GET['del'];
			$nid=$this->obj->DB_delete_all("message","(`id`='".$del."' OR `keyid`='".$del."') AND `fa_uid`='".$this->uid."'"," ");
			$nid?$this->layer_msg('删除成功！',9):$this->layer_msg('删除失败！',8);
		}
		$this->public_action();
		$urlarr=array("c"=>"seemessage","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("message","(`fa_uid`='".$this->uid."' or `uid`='".$this->uid."') and `keyid`='0' order by id desc",$pageurl,"10");
		$this->obj->DB_update_all("message","`remind_status`='1'","`fa_uid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("message1",'1');
		$this->user_tpl('seemessage');
	}
	function replys_action()
	{
		if($_POST['replys_message'])
		{
			$user_message = $this->obj->DB_select_once("message","(`fa_uid`='".$this->uid."' or `uid`='".$this->uid."') AND `id`='".intval($_POST['keyid'])."'");
			if(empty($user_message))
			{
				$this->obj->ACT_layer_msg("该信息不存在！",8,"index.php?c=seemessage");
			}else{
				$data['keyid']=intval($_POST['keyid']);
				$data['content']=$_POST['content'];
				$data['fa_uid']=$this->uid;
				$data['username']=$this->username;
				$data['ctime']=mktime();
				$nid=$this->obj->insert_into("message",$data);
				$this->obj->update_once('message',array('status'=>1),array('id'=>$data['keyid'],'fa_uid'=>$this->uid));

				$nid?$this->obj->ACT_layer_msg("回复成功！",9,"index.php?c=replys&id=".$_POST['keyid']):$this->obj->ACT_layer_msg("回复失败！",9,"index.php?c=replys&id=".$_POST['keyid']);
			}
		}
		if($_GET['id']){
			$id=(int)$_GET['id'];
			$user_message = $this->obj->DB_select_once("message","(`fa_uid`='".$this->uid."' or `uid`='".$this->uid."') AND `id`='".$id."'");
			if(is_array($user_message))
			{
				$urlarr=array("c"=>"replys","id"=>$id,"page"=>"{{page}}");
				$pageurl=$this->url("index","index",$urlarr);
				$this->get_page("message","`keyid`='".$id."' or `id`='".$id."' order by `id` desc",$pageurl,"10");
				$this->yunset("id",$id);
				$this->public_action();
				$this->user_tpl('replys');
			} else{
				$this->obj->ACT_msg("index.php?c=seemessage","该信息不存在！");
			}
		}
	}
	function xin_action()
	{
		if($_GET['del'])
		{
			if($_GET['id'])
			{
				$id=(int)$_GET['id'];
				$nid = $this->obj->DB_delete_all("friend_message","`id`='".$id."' and `uid`='".$this->uid."'");
			}else{
				$did=(int)$_GET['did'];

				$nid = $this->obj->update_once('friend_message',array('status'=>1),array('id'=>$did,'fid'=>$this->uid));
			}
			$nid?$this->layer_msg('删除成功！',9):$this->layer_msg('删除失败！',8);
 		}
		if($_POST["submit"]){
			$data['content'] = trim($_POST['content']);
			$data['ctime']   = time();
			$data['fid']     = intval($_POST['fid']);
			$data['uid']     = intval($this->uid);
			$nid=$this->obj->insert_into("friend_message",$data);
 			$nid?$this->obj->ACT_layer_msg("留言成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("留言失败！",8,$_SERVER['HTTP_REFERER']);
		}
		$where.= "`uid`='".$this->uid."' or (`fid`='".$this->uid."' and `status`<>'1') order by ctime desc";
		$urlarr["c"] = $_GET["c"];
		$urlarr["page"] = "{{page}}";
		$pageurl = $this->url("index","index",$urlarr);
		$rows=$this->get_page("friend_message",$where,$pageurl,"20");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uids[]=$v['uid'];
				$uids[]=$v['fid'];
			}
			$statis =$this->obj->DB_select_all("friend_info","`uid` in (".$this->pylode(",",$uids).")","uid,nickname");
			foreach($rows as $key=>$value)
			{
				$rows[$key]['ctime'] = date("Y-m-d H:i",$value['ctime']);
				foreach($statis as $k=>$v)
				{
					if($value['uid']==$v['uid'])
					{
						  $rows[$key]['nickname'] = $v['nickname'];
					}
					if($value['fid']==$v['uid'])
					{
						  $rows[$key]['name'] = $v['nickname'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->public_action();
		$this->obj->DB_update_all("friend_message","`remind_status`='1'","`fid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("friend_message1","1");
		$this->user_tpl('xin');
	}
	function expect_action()
	{
		if($this->config['user_enforce_identitycert']=="1"){
			$row=$this->obj->DB_select_once("resume","`idcard_pic`<>'' and `uid`='".$this->uid."'");
			if($row['idcard_status']!="1"){
				$this->obj->ACT_msg("index.php?c=verifica","请先完成身份认证！");
			}
		}
		if($_POST['shell']==1)
		{
			$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
			if($resume['name']=="")
			{
				echo 1;
			}die;
		}
		if($_GET['add'] == $this->uid)
		{
			$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
			if($num>=$this->config['user_number'])
			{
				$this->obj->ACT_msg("index.php?c=resume","你的简历数已经超过系统设置的简历数了");
			}
			echo "<script>location.href='index.php?c=expect'</script>";exit;
		}
		include APP_PATH."/plus/job.cache.php";
		include APP_PATH."/plus/city.cache.php";
		$job_area = $this->city_info($job_index,$job_name);
		$this->yunset("job_area",$job_area);
		$this->industry_cache();
		if($_GET['e']){
			$eid=(int)$_GET['e'];
			$row=$this->obj->DB_select_once("resume_expect","id='".$eid."' AND `uid`='".$this->uid."'");
			if(!is_array($row) || empty($row))
			{
				$this->obj->ACT_msg("index.php?c=resume","无效的简历！");
			}
			$job_classid=@explode(",",$row['job_classid']);
			if(is_array($job_classid)){
				foreach($job_classid as $key){
					$job_classname[]=$job_name[$key];
				}
				$this->yunset("job_classname",$this->pylode(' ',$job_classname));
				$this->yunset("job_classname2",$this->pylode(',',$job_classname));
			}
			$this->yunset("job_classid",$job_classid);
			$this->yunset("row",$row);

			$skill = $this->obj->DB_select_all("resume_skill","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("skill",$skill);

			$work = $this->obj->DB_select_all("resume_work","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("work",$work);

			$project = $this->obj->DB_select_all("resume_project","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("project",$project);

			$edu = $this->obj->DB_select_all("resume_edu","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("edu",$edu);

			$training = $this->obj->DB_select_all("resume_training","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("training",$training);

			$cert = $this->obj->DB_select_all("resume_cert","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("cert",$cert);

			$other = $this->obj->DB_select_all("resume_other","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("other",$other);
		}
		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		$this->yunset("resume",$resume);
		$this->public_action();
		$this->user_left();
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$this->CacheInclude($CacheArr);
		$this->yunset("js_def",2);
		$this->user_tpl('expect');
	}
	function saveexpect_action()
	{
		if($_POST['submit'])
		{
			$eid=(int)$_POST['eid'];
			unset($_POST['submit']);
			unset($_POST['eid']);
			unset($_POST['urlid']);
			$_POST['name'] = iconv("utf-8", "gbk", $_POST['name']);
			$where['id']=$eid;
			$where['uid']=$this->uid;
			$_POST['lastupdate']=time();
			if($eid=="")
			{
				$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
				if($num>=$this->config['user_number'])
				{
					echo 1;die;
				}
				$_POST['uid']=$this->uid;
				$nid=$this->obj->insert_into("resume_expect",$_POST);
				if ($nid)
				{
					if($num==0)
					{
						$this->obj->update_once('resume',array('def_job'=>$nid),array('uid'=>$this->uid));
					}
					$data['uid'] = $this->uid;
					$data['eid'] = $nid;
					$this->obj->insert_into("user_resume",$data);
					$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`+1","`uid`='".$this->uid."'");
					$state_content = "发布了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$nid\" target=\"_blank\">新简历</a>。";
					$fdata['uid']	  = $this->uid;
					$fdata['content'] = $state_content;
					$fdata['ctime']   = time();
					$this->obj->insert_into("friend_state",$fdata);
				}
				$eid=$nid;
			}else{
				$nid=$this->obj->update_once("resume_expect",$_POST,$where);
			}
			$row=$this->obj->DB_select_once("user_resume","`expect`='1'","`eid`='$eid'");
			$this->obj->update_once('user_resume',array('expect'=>1),array('eid'=>$eid,'uid'=>$this->uid));
			if($nid)
			{
				$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
				$numresume=$this->obj->complete($resume_row);
				$resume=$this->obj->DB_select_once("resume_expect","`id`='".$eid."'");
				$resume['numresume']=$numresume;
				include APP_PATH."/plus/user.cache.php";
				include APP_PATH."/plus/job.cache.php";
				include APP_PATH."/plus/city.cache.php";
				include APP_PATH."/plus/industry.cache.php";
				$resume['report']=$userclass_name[$resume['report']];
				$resume['hy']=$industry_name[$resume['hy']];
				$resume['city']=$city_name[$resume['provinceid']]." ".$city_name[$resume['cityid']]." ".$city_name[$resume['three_cityid']];
				$resume['salary']=$userclass_name[$resume['salary']];
				$resume['type']=$userclass_name[$resume['type']];
				if($resume['job_classid']!="")
				{
					$job_classid=@explode(",",$resume['job_classid']);
					foreach($job_classid as $v)
					{
						$job_classname[]=$job_name[$v];
					}
					$resume['job_classname']=$this->pylode(" ",$job_classname);
				}
				$resume['three_cityid']=$city_name[$resume['three_cityid']];
				if(is_array($resume))
				{
					foreach($resume as $k=>$v)
					{
						$arr[$k]=iconv("gbk","utf-8",$v);
					}
				}
				echo json_encode($arr);die;
			}else{
				echo 0;die;
			}
		}
	}
	function resume_del_action()
	{
		if($_POST['id']&&$_POST['table'])
		{
			$tables=array("skill","work","project","edu","training","cert","other");
			if(in_array($_POST['table'],$tables))
			{
				$table = "resume_".$_POST['table'];
				$eid=(int)$_POST['eid'];
				$id=(int)$_POST['id'];
				$url = $_POST['table'];
				$nid=$this->obj->DB_delete_all($table,"`id`='".$id."' and `uid`='".$this->uid."'");
				$this->obj->DB_update_all("user_resume","`".$url."`=`".$url."`-1","`eid`='".$eid."' and  `uid`='".$this->uid."'");
				$resume=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
				$resume[$url];
				if($nid)
				{
					$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
					$numresume=$this->obj->complete($resume_row);
					echo $numresume."##".$resume[$url];;die;
				}else{
					echo 0;die;
				}
			}else{
				echo 0;die;
			}
		}
	}
	function resume($table,$url,$nexturl,$name="")
	{
		if($_GET['del'])
		{
			$eid=(int)$_GET['e'];
			$del=(int)$_GET['del'];
			$nid=$this->obj->DB_delete_all($table,"`id`='".$del."' and `uid`='".$this->uid."'");
			$this->obj->DB_update_all("user_resume","`$url`=`$url`-1","`eid`='".$eid."' and `uid`='".$this->uid."'");
 			$nid?$this->layer_msg('删除成功！',9,0,"index.php?c=$url&e=".$eid):$this->layer_msg('删除失败！',8,0,"index.php?c=$url&e=".$eid);
		}
       if($_POST["submit"])
       {
			$eid=(int)$_POST['eid'];
			$id=(int)$_POST['id'];
			$_POST['uid']=$this->uid;
			unset($_POST['submit']);
			unset($_POST['id']);
			unset($_POST['table']);
			if($_POST['name'])$_POST['name'] = iconv("utf-8", "gbk", $_POST[name]);
			if($_POST['content'])$_POST['content'] = iconv("utf-8", "gbk", $_POST['content']);
			if($_POST['title'])$_POST['title'] = iconv("utf-8", "gbk", $_POST['title']);
			if($_POST['department'])$_POST['department'] = iconv("utf-8", "gbk", $_POST['department']);
			if($_POST['sys'])$_POST['sys'] = iconv("utf-8", "gbk", $_POST['sys']);
			if($_POST['specialty'])$_POST['specialty'] = iconv("utf-8", "gbk", $_POST['specialty']);
			if($_POST['sdate'])
			{
				$_POST['sdate']=strtotime($_POST['sdate']);
			}
			if($_POST['edate'])
			{
				$_POST['edate']=strtotime($_POST['edate']);
			}
			if(!$id)
			{
				$nid=$this->obj->insert_into($table,$_POST);
				$this->obj->DB_update_all("user_resume","`$url`=`$url`+1","`eid`='$eid' and `uid`='".$this->uid."'");
				if($nid)
				{
					$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
					$numresume=$this->obj->complete($resume_row);
					$this->select_resume($table,$nid,$numresume);
				}else{
					echo 0;die;
				}
			}else{
				$where['id']=$id;
				$nid=$this->obj->update_once($table,$_POST,$where);
				if($nid)
				{
					$this->select_resume($table,$id);
				}else{
					echo 0;die;
				}
			}
		}
		$rows=$this->obj->DB_select_all($table,"`eid`='".$eid."'");
		$this->yunset("rows",$rows);
	}
	function select_resume($table,$id,$numresume="")
	{
		include(PLUS_PATH."user.cache.php");
		$info=$this->obj->DB_select_once($table,"`id`='".$id."'");
		$info['skillval']=$userclass_name[$info['skill']];
		$info['ingval']=$userclass_name[$info['ing']];
		$info['sdate']=date("Y-m-d",$info['sdate']);
		$info['edate']=date("Y-m-d",$info['edate']);
		$info['numresume']=$numresume;
		if(is_array($info))
		{
			foreach($info as $k=>$v)
			{
				$arr[$k]=iconv("gbk","utf-8",$v);
			}
		}
		echo json_encode($arr);die;
	}
	function user_left()
	{
		if($_GET['e'])
		{
			$eid=(int)$_GET['e'];
			$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
			$this->yunset("resume_row",$resume_row);
			$numresume=$this->obj->complete($resume_row);
		}else{
			$numresume=20;
		}
		$this->yunset("numresume",$numresume);
		$this->obj->update_once('resume_expect',array('integrity'=>$numresume),array('uid'=>$this->uid));
		return $numresume;
	}
	function skill_action()
	{
		$this->resume("resume_skill","skill","expect","填写项目经验");
		$this->public_action();
	}
	function work_action()
	{
		$this->resume("resume_work","work","expect","填写项目经验");
		$this->public_action();
	}
	function project_action()
	{
		$this->resume("resume_project","project","edu","填写教育经历");
		$this->public_action();
		$this->user_tpl('project');
	}
	function edu_action()
	{
		$this->resume("resume_edu","edu","training","填写培训经历");
		$this->public_action();
		$this->user_tpl('edu');
	}
	function training_action()
	{
		$this->resume("resume_training","training","cert","填写证书");
		$this->public_action();
		$this->user_tpl('training');
	}
	function cert_action()
	{
		$this->resume("resume_cert","cert","other","填写其它");
		$this->public_action();
		$this->user_tpl('cert');
	}
	function other_action()
	{
		$this->resume("resume_other","other","resume","返回简历管理");
		$this->public_action();
		$this->user_tpl('other');
	}
	function expectq_action(){
		$this->get_user();
		$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
		if($_POST['submit']){
			$eid=(int)$_POST['eid'];
			$data['doc']=str_replace("&amp;","&",html_entity_decode($_POST['doc'],ENT_QUOTES,"GB2312"));
			$_POST['lastupdate']=mktime();
			unset($_POST['eid']);
			unset($_POST['submit']);
			unset($_POST['doc']);
			if(!$eid){
				if($num>=$this->config['user_number'])
				{
					$this->obj->ACT_layer_msg("你的简历数已经超过系统设置的简历数了",8,"index.php?c=resume");
				}
				$_POST['doc']='1';
				$_POST['uid']=(int)$this->uid;
				$nid=$this->obj->insert_into("resume_expect",$_POST);
				$data['eid']=(int)$nid;
				$data['uid']=(int)$this->uid;
				$nid2=$this->obj->insert_into("resume_doc",$data);
				if($nid2){
					if($num==0){
						$this->obj->update_once('resume',array('def_job'=>$nid),array('uid'=>$this->uid));
 					}
					$nid2=$this->obj->DB_update_all("member_statis","`resume_num`=`resume_num`+1","uid='".$this->uid."'");
				}
				$nid2?$this->obj->ACT_layer_msg("添加成功！",9,"index.php?c=resume"):$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=resume");
			}else{
				$_POST['height_status']='0';
				$this->obj->update_once("resume_expect",$_POST,array("id"=>$eid));
				$nid=$this->obj->update_once("resume_doc",$data,array("eid"=>$eid));
 				$nid?$this->obj->ACT_layer_msg("更新成功！",9,"index.php?c=resume"):$this->obj->ACT_layer_msg("更新失败！",8,"index.php?c=resume");
			}
		}
		if($num>=$this->config['user_number']&&$_GET['e']=='')
		{
			$this->obj->ACT_msg("index.php?c=resume","你的简历数已经超过系统设置的简历数了");
		}
		$row=$this->obj->DB_select_alls("resume_expect","resume_doc","a.`uid`='".$this->uid."' and a.`id`='".(int)$_GET['e']."' and a.`id`=b.eid");
		$this->yunset("row",$row[0]);

		if($row[0]['job_classid'])
		{
			include APP_PATH."/plus/job.cache.php";
			$job_classid=@explode(",",$row[0]['job_classid']);
			if(is_array($job_classid)){
				foreach($job_classid as $key){
					$job_classname[]=$job_name[$key];
				}
				$this->yunset("job_classname",@implode('+',$job_classname));
			}
			$this->yunset("job_classid",$job_classid);
		}
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		$this->user_cache();
		$this->yunset("js_def",2);
		$this->user_tpl('expectq');
	}
	function privacy_action()
	{
		if(intval($_POST['status'])){
			$this->obj->update_once('resume',array('status'=>intval($_POST['status'])),array('uid'=>$this->uid));
			exit();
		}
		$resume = $this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`status`");
        $this->yunset("status",$resume['status']);
		$this->public_action();
		$this->yunset("js_def",2);
		$this->user_tpl('privacy');
	}
	function look_action()
	{
		if($_GET['del'])
		{
			$this->obj->DB_delete_all("look_resume","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
 			$this->layer_msg('删除成功！',9,0,"index.php?c=look");
		}
		$urlarr=array("c"=>"look","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$look=$this->get_page("look_resume","`uid`='".$this->uid."'",$pageurl,"10");

		if(is_array($look))
		{
			foreach($look as $k=>$v)
			{
				$com_uid[] = $v['com_id'];
				$res_uid[] = $v['resume_id'];
			}
			$resume=$this->obj->DB_select_all("resume_expect","`id` IN (".$this->pylode(",",$res_uid).")","id,name");
			$com=$this->obj->DB_select_all("company","`uid` IN (".$this->pylode(",",$com_uid).")","uid,name");
			foreach($look as $k=>$v)
			{
				foreach($resume as $key=>$val)
				{
					if($v['resume_id']==$val['id'])
					{
						$look[$k]['resume']=$val['name'];
					}
				}
				foreach($com as $value)
				{
					if($v['com_id']==$value['uid'])
					{
						$look[$k]['com']=$value['name'];
					}
				}
			}
		}
		$this->yunset("js_def",2);
		$this->yunset("look",$look);
		$this->public_action();
		$this->user_tpl('look');
	}

	function verifica_action()
	{
		$this->public_action();
		$this->get_user();
		if($_POST['submit'])
		{
			$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","idcard_pic");
			$this->obj->unlink_pic($resume['idcard_pic']);
			$upload=$this->upload_pic("../upload/user/",false,$this->config['user_pickb']);
			$pictures=$upload->picture($_FILES['pic']);
			$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
			if($this->config['user_idcard_status']=="1")
			{
				$status='0';
			}else{
				$status='1';
				$this->obj->update_once('friend_info',array('iscert'=>$status),array('uid'=>$this->uid));
			}
			$id=$this->obj->update_once('resume',array('idcard_pic'=>$pictures,'idcard_status'=>$status,'cert_time'=>mktime()),array('uid'=>$this->uid));

 			$id?$this->obj->ACT_layer_msg("上传成功",9,"index.php?c=verifica"):$this->obj->ACT_layer_msg("上传失败",8,"index.php?c=verifica");
		}
		$row=$this->obj->DB_select_once("resume","`idcard_pic`<>'' and `uid`='".$this->uid."'");
		$this->yunset("row",$row);
		$this->user_tpl('verifica');
	}
	function my_question_action()
	{
		$this->public_action();
		if($_GET["type"]==0)
		{
			$table="question";
		}elseif($_GET["type"]==1){
			$table="answer";
		}elseif($_GET["type"]==2){
			$table="answer_review";
		}
		include(LIB_PATH."page3.class.php");
		$limit=10;
		$page=$_GET["page"]<1?1:$_GET["page"];
		$ststrsql=($page-1)*$limit;
		$page_url = "index.php?c=".$_GET['c']."&type=".$_GET["type"]."&page={{page}}";
		$num = $this->obj->DB_select_num($table,"`uid`='".$this->uid."'");
		$pages=ceil($num/$limit);
		$page = new page($page,$limit,$num,$page_url);
		$pagenav=$page->numPage();
		if($_GET["type"]==0)
		{
			$list = $this->obj->DB_select_all($table,"`uid`='".$this->uid."'  ORDER BY `add_time` DESC LIMIT $ststrsql,$limit");
		}else{
			$list = $this->obj->DB_select_alls($table,"question","a.`uid`='".$this->uid."' and a.`qid`=b.`id`  ORDER BY a.`add_time` DESC LIMIT $ststrsql,$limit","a.`content`,a.`add_time`,b.`id`,b.`title`,a.`id` as `aid`");
		}
		if($list[0]!='')
		{
			$this->yunset("q_list",$list);
		}
		$this->yunset("gettype",$_GET["type"]);
		$this->yunset("pagenav",$pagenav);
		$this->user_tpl('my_question');
	}
	function delask_action(){
		$del=(int)$_GET['id'];
		$is_del=$this->obj->DB_delete_all("question","`id`='".$del."' and uid='".$this->uid."'");
		if(!empty($is_del)){
			$this->obj->DB_delete_all("answer","`qid`='".$del."'","");
			$d_a=$this->obj->DB_delete_all("answer_review","`qid`='".$del."'","");
		}
		$d_a?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
	}


	function resume_ajax_action()
	{
		include(PLUS_PATH."user.cache.php");
		$table="resume_".$_POST['type'];
		$table_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc");
		if(!in_array($table,$table_array))
		{
			exit();
		}
		$id=(int)$_POST['id'];
		$info=$this->obj->DB_select_once($table,"`id`='".$id."'");
		$info['skillval']=$userclass_name[$info['skill']];
		$info['ingval']=$userclass_name[$info['ing']];
		$info['sdate']=date("Y-m-d",$info['sdate']);
		$info['edate']=date("Y-m-d",$info['edate']);
		if(is_array($info))
		{
			foreach($info as $k=>$v)
			{
				$arr[$k]=iconv("gbk","utf-8",$v);
			}
		}
		echo json_encode($arr);die;
	}
	function searchcom_action()
	{
		if($_GET['c_uid'])
		{
			$uid=(int)$_GET['c_uid'];
			$row=$this->obj->DB_select_once("blacklist","`c_uid`='".$uid."' and `p_uid`='".$this->uid."'");
			if(!empty($row))
			{
				$this->layer_msg('您已屏蔽够该用户！',8,0,"index.php?c=searchcom");
			}
			$com=$this->obj->DB_select_once("company","`uid`='".$uid."'","`name`");
			$data['c_uid']=$uid;
			$data['p_uid']=$this->uid;
			$data['inputtime']=time();
			$data['usertype']="1";
			$data['com_name']=$com['name'];
			$nid=$this->obj->insert_into("blacklist",$data);
			$nid?$this->layer_msg('屏蔽成功！',9,0,"index.php?c=blacklist"):$this->layer_msg('屏蔽失败！',8,0,"index.php?c=searchcom");
		}
		if($_POST['ids'])
		{
			$ids=@explode(",",$_POST['ids']);
			foreach($ids as $v)
			{
				$row=$this->obj->DB_select_once("blacklist","`c_uid`='".$v."' and `p_uid`='".$this->uid."'");
				if(empty($row))
				{
					$com=$this->obj->DB_select_once("company","`uid`='".$v."'","`name`");
					$data=array();
					$data['c_uid']=$v;
					$data['p_uid']=$this->uid;
					$data['inputtime']=time();
					$data['usertype']="1";
					$data['com_name']=$com['name'];
					$this->obj->insert_into("blacklist",$data);
				}
			}
			die;
		}
		if($_GET['keyword'])
		{
			$where="`name` like '%".$_GET['keyword']."%'";
			$urlarr=array("c"=>"searchcom","page"=>"{{page}}");
			$pageurl=$this->url("index","index",$urlarr);
			$rows=$this->get_page("company",$where,$pageurl,"10");
			if(is_array($rows))
			{
				include APP_PATH."/plus/city.cache.php";
				foreach($rows as $v)
				{
					$c_uid[]=$v['uid'];
				}
				$black=$this->obj->DB_select_all("blacklist","`p_uid`='".$this->uid."' and `c_uid` in (".@implode(",",$c_uid).")");
				foreach($rows as $k=>$v)
				{
					$rows[$k]['provinceid']=$city_name[$v['provinceid']];
					$rows[$k]['cityid']=$city_name[$v['cityid']];
					foreach($black as $val)
					{
						if($v['uid']==$val['c_uid'])
						{
							$rows[$k]['status']="1";
						}
					}
				}
			}
		}

		$this->yunset("rows",$rows);
		$this->public_action();
		$this->user_tpl('searchcom');
	}
}
?>