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
class wx_controller extends common
{
	function index_action(){
	
		$this->yuntpl(array('admin/admin_wx'));
	}

	function save_action(){
 		if($_POST["config"]){
			unset($_POST["config"]);
			foreach($_POST as $key=>$v){
		    	$config=$this->obj->DB_select_num("admin_config","`name`='$key'");
			   if($config==false){
				$this->obj->DB_insert_once("admin_config","`name`='$key',`config`='".iconv("utf-8", "gbk", $v)."'");
			   }else{
				$this->obj->DB_update_all("admin_config","`config`='".iconv("utf-8", "gbk", $v)."'","`name`='$key'");
			   }
		 	}
			$this->web_config();
			$this->obj->ACT_layer_msg("微信配置更新成功！",9,1,2,1);

		}
	}

	function binduser_action(){
		
 		$where = "`wxid`!=''";
		if($_GET['keyword']){
			$where.=" and `username` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$order = " ORDER BY `wxbindtime` DESC";
		$urlarr['c']=$_GET['c'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$userList=$this->get_page("member",$where.$order,$pageurl,$this->config['sy_listnum'],"`uid`,`username`,`wxid`,`wxbindtime`");
		
		$this->yunset("userList",$userList);
		$this->yuntpl(array('admin/admin_wxbind'));
	}

	function keyword_action(){
		
 		$where = "`type`='8'";
		if(trim($_GET['keyword'])){
			$where.=" and `key_name` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=trim($_GET['keyword']);
		}
		$order = " ORDER BY `num` DESC";
		$urlarr['c']=$_GET['c'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$keyList=$this->get_page("hot_key",$where.$order,$pageurl,$this->config['sy_listnum']);
		
		$this->yunset("keyList",$keyList);
		$this->yuntpl(array('admin/admin_wxkey'));
	}

	function wxnav_action(){
		
  		$list = $this->obj->DB_select_all("wxnav","1 ORDER BY `sort` ASC");

		
		if(is_array($list)){
			foreach($list as $value){
				if($value['keyid']=='0'){
					$navlist[$value['id']] = $value;
				}
			}
			foreach($list as $val){
				foreach($navlist as $key=>$v){
					if($v['id']==$val['keyid']){
						$navlist[$key]['list'][] = $val;
					}
				}
			} 
		}
		$this->yunset('navlist',$navlist); 
		$this->yuntpl(array('admin/admin_wxnav'));
	}
	
	function wxlog_action(){
		
 		$where = '1'; 
		if(trim($_GET['keyword'])){
			$where.=" and `wxname` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=trim($_GET['keyword']);
		}
		$urlarr['c']="wxlog";
		$urlarr['page']="{{page}}";
		$order = " ORDER BY `time` DESC";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$logList=$this->get_page("wxlog",$where.$order,$pageurl,$this->config['sy_listnum']);
		
		$this->yunset("logList",$logList);
		$this->yuntpl(array('admin/admin_wxlog'));
	}
	function dellog_action(){
		$this->check_token();
		if($_GET['del']){
			$this->obj->DB_delete_all("wxlog","`id` in(".@implode(',',$_GET['del']).")","");	
			$this->layer_msg('操作日志(ID:'.@implode(',',$_GET['del']).')删除成功！',9,1,$_SERVER['HTTP_REFERER']);
		}
	}
	function getToken()
	{
 		$Token = $this->config['token'];
		$TokenTime = $this->config['token_time'];
		$NowTime = time();
	
 		if(($NowTime-$TokenTime)>7000)
		{	
			$Appid       = $this->config['wx_appid'];
			$Appsecert   = $this->config['wx_appsecret'];
			$Url         = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$Appid.'&secret='.$Appsecert;
			$CurlReturn  = $this->CurlPost($Url);
			$Token       = json_decode($CurlReturn);
			
			$this->config['token']      = $Token->access_token;
			$this->config['token_time'] = time();
 			include_once(LIB_PATH."/public.function.php");

			$this->obj->made_web(PLUS_PATH."config.php",ArrayToString($this->config),"config");
			return $this->config['token'];
		}else{
			return $Token;
		}
	}

 	
	public function edit_action()
	{
		
		if($_POST['name'] && $_POST['keyid']!=='')
		{
			
			$_POST['name'] = iconv('utf-8','gbk',$_POST['name']);
			$_POST['key'] = iconv('utf-8','gbk',$_POST['key']);
			$where = "`name`='".$_POST['name']."'";
			if($_POST['keyid']>0)
			{
				if(!$_POST['key'] && $_POST['type']!='view')
				{
					echo 1;
					exit();
				}elseif($_POST['key']!=""){
					
					$where = "(`name`='".$_POST['name']."' OR `key`='".$_POST['key']."')";
				}
			}
			if($_POST['navid']>0)
			{
				$where .= " AND  `id`!='".$_POST['navid']."'";
			}

 			$nav = $this->obj->DB_select_num("wxnav",$where);
			if($nav>0)
			{
				echo 2;
				exit();
			}
			if($_POST['keyid']=='0') 
			{
				$_POST['type']= 'click';
				unset($_POST['url']);unset($_POST['key']);
			}
			unset($_POST['pytoken']);
			if($_POST['navid']>0)
			{
				$navid = $_POST['navid'];
				unset($_POST['navid']);
			
				$this->obj->update_once("wxnav",$_POST,array('id'=>$navid));	
			}else{ 
				$this->obj->insert_into("wxnav",$_POST);
			}
			
			echo 3;
			exit();
		}else{
			echo 1;
			exit();
		}

	}
 	public function creat_action()
	{
 		$list = $this->obj->DB_select_all("wxnav","1 ORDER BY `keyid` ASC,`sort` ASC");

		if(is_array($list))
		{
			foreach($list as $key=>$value)
			{
				if($value['keyid']>0)
				{
					$navlist[$value['keyid']]['list'][] = $value;
				}else{
					$navlist[$value['id']] = $value;
				}
			}
			$CreatNav = '{"button":[';
			$i=0;
			foreach($navlist as $key=>$value)
			{
				if($i<1)
				{
					$CreatNav.='{"name":"'.iconv('gbk','utf-8',$value['name']).'","sub_button":[';
				}else{
					$CreatNav.=',{"name":"'.iconv('gbk','utf-8',$value['name']).'","sub_button":[';
				}
				$i++;
				$NavInfo = array();
				
				if(is_array($value['list']) && !empty($value['list'])){
					
					foreach($value['list'] as $k=>$v)
					{
						if($k>0)
						{
							$CreatNav.=',';
						}
						if($v['type']=='click')
						{
							$CreatNav.='{"type":"click","name":"'.iconv('gbk','utf-8',$v['name']).'","key":"'.iconv('gbk','utf-8',$v['key']).'"}';
							
						}elseif($v['type']=='view'){
							
							$CreatNav.='{"type":"view","name":"'.iconv('gbk','utf-8',$v['name']).'","url":"'.$v['url'].'"}';
						}
					}
				}
				$CreatNav.=']}';
			}
			$CreatNav.=']}';
 			$Token = $this->getToken();
			
 			$DelUrl = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$Token;
			$this->CurlPost($DelUrl);
			
			$Url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$Token;
			$result = $this->CurlPost($Url,$CreatNav);
			$Info = json_decode($result);
			
			if($Info->errcode=='0' || $Info->errmsg=='ok'){
 				echo 1;die;
			}else{ 
 				echo 2;die;
			}
		}
	}

	function CurlPost($url,$data='')
	{ 
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		if($data!='')
		{
			curl_setopt($ch, CURLOPT_POST, 1);  
   			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$Return=curl_exec ($ch);
		if (curl_errno($ch)) {
		   echo 'Errno'.curl_error($ch); 
		}
		curl_close($ch);  

		return $Return;  
	}
 	function delnav_action(){ 
		if($_POST['del']){  
			$this->obj->DB_delete_all("wxnav","`id` in(".@implode(',',$_POST['del']).")",'');	
			$this->layer_msg('微信菜单(ID:'.@implode(',',$_POST['del']).')删除成功！',9,1,$_SERVER['HTTP_REFERER']);
		}
		if((int)$_GET['delid']){
			$this->check_token();
			$id=$this->obj->DB_delete_all("wxnav","`id`='".$_GET['delid']."'");			
			$id?$this->layer_msg('微信菜单(ID:'.$_GET['delid'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
 	function deluser_action(){
		if($_GET['del']){
			$this->check_token();
			$this->obj->DB_update_all("member","`wxid`=''","`uid` in(".@implode(',',$_GET['del']).")");	
			$this->layer_msg('微信用户(ID:'.@implode(',',$_GET['del']).')取消绑定成功！',9,1,$_SERVER['HTTP_REFERER']);
		}
	} 
	function ajax_action()
	{
		if($_POST['sort']) 
		{
			$this->obj->DB_update_all("wxnav","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
		}
		if($_POST['name']) 
		{
			$_POST['name']=iconv("UTF-8","gbk",$_POST['name']);
			$this->obj->DB_update_all("wxnav","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
		}
		echo '1';die;
	}
	
	function array_iconv($in_charset,$out_charset,$arr){    
        return eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));    
	}    
}

?>