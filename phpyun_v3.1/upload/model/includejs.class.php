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
class includejs_controller extends common
{
	function RedLoginHead_action(){
		if($_COOKIE["uid"]!=""&&$_COOKIE["username"]!=""){
			$html = "您好：<a href=\"".$this->config['sy_weburl']."/member\" ><font color=\"red\">".$_COOKIE['username']."</font></a>！<a href=\"javascript:void(0)\" onclick=\"logout(\'".$this->config['sy_weburl']."/index.php?c=logout\');\">[安全退出]</a>&nbsp;";
			$html.='<span class="header_Remind header_Remind_hover" onmousemove="Remind_show();"> <em class="header_Remind_em">提醒（<b>'.$_COOKIE['remind_num'].'</b>）</em><div class="header_Remind_list" style="display:none;"><div class="header_Remind_close"><a href="javascript:Remind_hide();" title="关闭"></a></div>';
			if($_COOKIE['usertype']==1)
			{
				$html.='<p class="header_Remind_icon1"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=msg">邀请面试(<font color="#FF0000">'.$_COOKIE['userid_msg'].'</font>)</a></p><p class="header_Remind_icon2"><a href="'.$this->config['sy_weburl'].'/friend/index.php?c=applyfriend">邀请好友(<font color="#FF0000">'.$_COOKIE['friend1'].'</font>)</a></p><p class="header_Remind_icon3"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=xin">私信(<font color="#FF0000">'.$_COOKIE['friend_message1'].'</font>)</a></p><p class="header_Remind_icon4"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=seemessage">站内信(<font color="#FF0000">'.$_COOKIE['message1'].'</font>)</a></p><p class="header_Remind_icon5"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=commsg">企业回复咨询(<font color="#FF0000">'.$_COOKIE['usermsg'].'</font>)</a></p>';
			}elseif($_COOKIE['usertype']==2){
				$html.='<p class="header_Remind_icon1"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=hr">申请职位(<font color="#FF0000">'.$_COOKIE['userid_job'].'</font>)</a></p><p class="header_Remind_icon2"><a href="'.$this->config['sy_weburl'].'/friend/index.php?c=applyfriend">邀请好友(<font color="#FF0000">'.$_COOKIE['friend2'].'</font>)</a></p><p class="header_Remind_icon3"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=xin"> 私信(<font color="#FF0000">'.$_COOKIE['friend_message2'].'</font>)</a></p><p class="header_Remind_icon4"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=seemessage"> 站内信(<font color="#FF0000">'.$_COOKIE['message2'].'</font>)</a></p><p class="header_Remind_icon5"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=msg">求职咨询(<font color="#FF0000">'.$_COOKIE['commsg'].'</font>)</a></p>';
			}

			$html.='</div></span> |';

			echo "document.write('".$html."');";
		}else{
			$login_url = $this->url("index","login",array("usertype"=>"1"),"1");
			$login_com_url = $this->url("index","login",array("usertype"=>"2"),"1");
			$qq_url = $this->url("index","qqconnect",array("c"=>"qqlogin"));
			$sina_url = $this->url("index","sinaconnect");
			$style = $this->config['sy_weburl']."/template/".$this->config['style'];
			if($this->config['sy_qqlogin']=="1"){
				$qq_html='<a href="'.$this->config['sy_weburl'].'/'.$qq_url.'" style="text-decoration:none;"><img src="'.$style.'/images/yun_qq.png" style="margin-right:3px; float:left; margin-top:5px;" class="png"><span>登录</span></a> |';
			}
			if($this->config['sy_sinalogin']=="1"){
				$sina_html='<a href="'.$this->config['sy_weburl'].'/'.$sina_url.'" style="text-decoration:none;"><img src="'.$style.'/images/yun_sina.png" style="margin-right:3px;" class="png"><span>登录</span></a> |';
			}
			$html = $qq_html.$sina_html.' <a href="'.$this->config['sy_weburl'].'/'.$login_url.'" class="top_logoin icon2">个人登录</a> | <a href="'.$this->config['sy_weburl'].'/'.$login_com_url.'"  class="top_logoin2 icon2">企业登录</a> |';
			echo "document.write('".$html."');";
		}
	}
	function DefaultLoginIndex_action()
	{
		if($_COOKIE['usertype']=='1' && $this->uid)
		{
			$member=$this->obj->DB_select_alls("member_statis","resume","a.`uid`='".$this->uid."' and b.`uid`='".$this->uid."'","a.*,b.`photo`");
			if($member[0]['photo']==''){
				$member[0]['photo']=$this->config['sy_weburl']."/".$this->config['sy_member_icon'];
			}
			$this->yunset("member",$member[0]);
		}else if($_COOKIE['usertype']=='2' && $this->uid){
			$company=$this->obj->DB_select_alls("company_statis","company","a.`uid`='".$this->uid."' and b.`uid`=a.`uid`","a.`job`,a.`sq_job`,a.`fav_job`,b.`logo`,a.`status2`");
			if($company[0]['logo']==''){
				$company[0]['logo']=$this->config['sy_weburl']."/".$this->config['sy_unit_icon'];
			}
			$this->yunset("company",$company[0]);
		}
		$this->yunset("cookie",$_COOKIE);
		$this->yun_tpl(array('login'));
	}

	function Site_action(){
		if($this->config["sy_web_site"]=="1"){
			if($_SESSION["cityname"]){
				$cityname = $_SESSION["cityname"];
			}else{
				$cityname = $this->config["sy_indexcity"];
			}
			$site_url = $this->config['sy_weburl']."/".$this->url("index","index",array("c"=>"site"),"1");
		    $html = "<div class=\"heder_city_line  icon2\"><div class=\"header_city_h1\">".$cityname."</div><div class=\"header_city_more icon2\"><a href=\"".$site_url."\">更多城市</a></div></div>";
		} echo "document.write('".$html."');";
	}
	function SiteCity_action()
	{
		if($_POST[cityid]=="nat")
		{
			unset($_SESSION["cityid"]);unset($_SESSION["three_cityid"]);unset($_SESSION["cityname"]);unset($_SESSION["hyclass"]);
			if($this->config["sy_indexdomain"])
			{
				$_SESSION["host"] = $this->config["sy_indexdomain"];

			}else{
				
				$_SESSION["host"] = $this->config["sy_weburl"];
			}
			echo $_SESSION["host"];
			die;
		}
		unset($_SESSION["cityid"]);unset($_SESSION["three_cityid"]);unset($_SESSION["cityname"]);unset($_SESSION["newsite"]);unset($_SESSION["host"]);unset($_SESSION["did"]);unset($_SESSION["hyclass"]);
		if((int)$_POST["cityid"]>0)
		{
			if(file_exists(APP_PATH."/plus/domain_cache.php"))
			{
				include(APP_PATH."/plus/domain_cache.php");

				if(is_array($site_domain))
				{
					foreach($site_domain as $key=>$value)
					{
						if($value["cityid"]==$_POST["cityid"] || $value["three_cityid"]==$_POST["cityid"])
						{
							$_SESSION["host"] = $value["host"];
						}
						if($value["three_cityid"]==$_POST["cityid"])
						{
							$_SESSION["three_cityid"] = $value["three_cityid"];
						}
					}
				}
			}
			if($_SESSION["host"] && "http://".$_SESSION["host"]==$this->config['sy_weburl'] )
			{
				$_SESSION[newsite]="new";
			}
			$_SESSION["host"] = $_SESSION['host']!=""?"http://".$_SESSION['host']:$this->config['sy_weburl'];
			if(!$_SESSION["three_cityid"]){
				$_SESSION["cityid"] = $_POST["cityid"];
			}
			$_SESSION["cityname"] = iconv("utf-8","gbk",$_POST["cityname"]);
			echo $_SESSION["host"];
			die;
		}else{
			$this->obj->ACT_layer_msg("传递了非法参数！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function SiteHy_action(){
		if($_POST['hyid']=="0"){
			unset($_SESSION["cityid"]);unset($_SESSION["three_cityid"]);unset($_SESSION["cityname"]);unset($_SESSION["hyclass"]);
			$_SESSION["host"] = $this->config["sy_indexdomain"];
			echo $_SESSION["host"];die;
		}
		unset($_SESSION["cityid"]);
		unset($_SESSION["three_cityid"]);
		unset($_SESSION["cityname"]);
		unset($_SESSION["newsite"]);
		unset($_SESSION["host"]);
		unset($_SESSION["did"]);
		unset($_SESSION["hyclass"]);
		if((int)$_POST["hyid"]>0)
		{
			if(file_exists(APP_PATH."/plus/domain_cache.php"))
			{
				include(APP_PATH."/plus/domain_cache.php");

				if(is_array($site_domain))
				{
					foreach($site_domain as $key=>$value)
					{
						if($value["hy"]==$_POST["hyid"])
						{
							$_SESSION["host"] = $value["host"];
						}
					}
				}
			}
			if($_SESSION["host"] && "http://".$_SESSION["host"]==$this->config['sy_weburl'] )
			{
				$_SESSION['newsite']="new";
			}
			$_SESSION["host"] = $_SESSION['host']!=""?"http://".$_SESSION['host']:$this->config['sy_weburl'];
			$_SESSION["hyclass"] = $_POST["hyid"];
			echo $_SESSION["host"];die;
		}else{
			$this->obj->ACT_layer_msg("传递了非法参数！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}