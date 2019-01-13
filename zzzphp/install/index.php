<?php
include '../inc/zzz_class.php';
  $GLOBALS['err']='0';
  $act =getform("act","both");
   if (check_file('install.lock')) error('很抱歉，程序已安装,如需重新安装请删除install/install.lock');
   if (conf('isinstall')==1 && empty($act)) error('很抱歉，程序已安装,如需重新安装请修改config配置信息,isinstall=>0');
	switch ($act){
		case "step1":
			$tpl=	template_parse(load_file('tpl/step1.tpl'));
			break;	
		case "step2":
			$tpl=	template_parse(load_file('tpl/step2.tpl'));
			break;	
		case "step3":
			$admin_pass=md5_16(get_session('admin_pass'));
			db_update("language",'lid=1',array('sitetitle'=>get_session('sitetitle'),'siteurl'=>get_session('siteurl')));
			db_update("user",'uid=1',array('username'=>get_session('admin_name'),'password'=>$admin_pass,'question'=>get_session('question'),'answer'=>get_session('answer')));
			$tpl=	template_parse(load_file('tpl/step3.tpl'));
			break;	
		case "step4":
			$tpl=	template_parse(load_file('tpl/step4.tpl'));
			break;		
		case "step5":
			db_update("language",'lid=1',array('sitepclogo'=>SITE_PATH."images/logo.png",'sitewaplogo'=>SITE_PATH."images/waplogo.png",'copyright'=> '版权所有 ©2015-'.date('Y').' zzcms.com'));
			create_file('install.lock','zzzcms安装锁定文件，删除后方可再次安装！');
			$tpl=	template_parse(load_file('tpl/step5.tpl'));				
			break;
		case "install":
			install();
			break;
		case "drop":
			drop();
			break;
		case "progress":
			progress();
			break;	
		case "testdata":
			testdata();
			break;	
		case"check_mysql":
			check_mysql();
			break;			
		default:			
			$tpl=	template_parse(load_file('tpl/index.tpl'));
	}
	if(isset($tpl)){
		file_put_contents(RUN_DIR.'tmp.php', $tpl);
		require RUN_DIR.'tmp.php';
		//unlink(RUN_DIR.'tmp.php'); 
	}
	function num_ch($num,$yes,$no){
		return $num ? $yes : $no;
	}
	function check_chinese(){
		$string=togbk($_SERVER['DOCUMENT_ROOT']);
		if(ifch($string)){
			$GLOBALS['err']='1';
			return '<b>网站路径中不能含有中文！</b>';
		}else{
			return $_SERVER['DOCUMENT_ROOT'];
		}
	}
	function check_version(){
		if (PHP_VERSION < '5.3') {
			$GLOBALS['err']='1';
			return '<b>'.PHP_VERSION.'不满足</b>';
		}else{
		   return PHP_VERSION;
		}
	}
	function drop(){
		$db=conf('db');
		$dbname=$db['name'];
		$con = mysqli_connect($db['host'],$db['user'],$db['password'],$dbname);
		{ 
   			 echo "连接 MySQL 失败: " . mysqli_connect_error(); 
		} 
		$result = mysqli_query($con,"show table status from $dbname");
		while($data=mysqli_fetch_array($result)) {
			mysqli_real_query($con,"drop table $data[Name]");
		}
		phpgo('?act=step2');
	}
	function check_mysql(){
		$host=getform("host","post");
		$port=getform("port","post");
		$dbname=getform("name","post");
		$user=getform("user","post");
		$password=getform("password","post");
		try {
			$attr = array(PDO::ATTR_TIMEOUT => 1);
			$trypdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password, $attr);
		} catch (PDOException $e) {
			$err_code=$e->getCode();
			switch ($err_code){
			  case '1049':
				echo(0);die;
				break;
			  case '1045':	
			    echo('1045');die;
				break;
			  case '2002':	
			    echo('2002');die;
				break;
			  default:
			  die($e->getMessage());
			}
		}
		echo 1;	
}
	function install(){	
		set_session('sitetitle',getform("sitetitle","post"));
		set_session('siteurl',getform("siteurl","post"));
		$sitepath=getform("sitepath","post");
		if(empty($sitepath))		$sitepath='/';
		if(cright($sitepath)!='/')	$sitepath.='/';
		$adminpath=getform("adminpath","post",'nul');
		if(cright($adminpath)!='/') $adminpath.='/';		
		$dbtype=getform("dbtype","post",'sel');		
		$sqlitepath=getform("sqlitepath","post");
		$sqlitename=getform("sqlitename","post");
		$accesspath=getform("accesspath","post");
		$accessname=getform("accessname","post");
		$host=getform("host","post");
		$port=getform("port","post");
		$dbname=getform("name","post");
		$user=getform("user","post");
		$password=getform("password","post");
		set_session('admin_name',getform("admin_name","post"));
		set_session('admin_pass',getform("admin_pass","post"));
		set_session('question',getform("question","post"));
		set_session('answer',getform("answer","post"));	
		$oldpath=conf('adminpath'); if(empty($oldpath) || !file_exists(SITE_DIR.$oldpath)) $oldpath='admin/';
		if(!file_exists(SITE_DIR.$oldpath)) back('未找到默认管理目录，请手动修改管理目录名改为admin');
	switch 	($dbtype){
		case 'access' :
		copy_file ('data/access.data',SITE_DIR.$accesspath.$accessname);
		save_config(array('sitepath'=>$sitepath,'adminpath'=>$adminpath,'type'=>$dbtype,'accesspath'=>$accesspath,'accessname'=>$accessname));
		break;	
		case 'sqlite' :
		copy_file ('data/sqlite.data',SITE_DIR.$sqlitepath.$sqlitename);
		save_config(array('sitepath'=>$sitepath,'adminpath'=>$adminpath,'type'=>$dbtype,'sqlitepath'=>$sqlitepath,'sqlitename'=>$sqlitename));
		break;	
		case 'mysql'  :
		//读取文件内容
		try {
			$attr = array(PDO::ATTR_TIMEOUT => 5);
			$trypdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password, $attr);			
			$result = $trypdo->query("SHOW TABLES LIKE 'zzz_language'");
			$row = $result->fetchAll();
			if(count($row)>0){
				save_config(array('sitepath'=>$sitepath,'adminpath'=>$adminpath,'type'=>$dbtype,'host'=>$host,'port'=>$port,'name'=>$dbname,'user'=>$user,'password'=>$password));
			  	confirm('数据库已存在，是否删除重建？\r\n【确定】删除\r\n【取消】返回','?act=drop','-1');exit();;
			}
		} catch (Exception $e) {
			try {
			$newpdo = new PDO("mysql:host=$host;port=$port", $user, $password, $attr);
				if($newpdo->query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci")== FALSE) {
					back("数据库连接成功，创建数据库失败，请尝试手动创建");		
				}
			} catch (Exception $e) {
				back('连接数据库出错,请检查数据库信息是否正确。');			
			}
			$newpdo=NULL;
	    }
		$trypdo	=NULL;
		save_config(array('sitepath'=>$sitepath,'adminpath'=>$adminpath,'type'=>$dbtype,'host'=>$host,'port'=>$port,'name'=>$dbname,'user'=>$user,'password'=>$password));
		$link = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password, $attr);
		$link->query("set names utf8");
		$link->query("CREATE TABLE `zzz_language`(`lid`int(11)NOT NULL AUTO_INCREMENT,`l_name`varchar(255)DEFAULT NULL,`l_path`varchar(255)DEFAULT NULL,`l_order`int(11)DEFAULT NULL,`l_onoff`int(1)DEFAULT NULL,`l_alias`varchar(255)DEFAULT NULL,`pctemplate`varchar(255)DEFAULT NULL,`waptemplate`varchar(255)DEFAULT NULL,`pchtmlpath`varchar(255)DEFAULT NULL,`waphtmlpath`varchar(255)DEFAULT NULL,`sitetitle`varchar(255)DEFAULT NULL,`additiontitle`varchar(255)DEFAULT NULL,`sitepclogo`varchar(255)DEFAULT NULL,`sitewaplogo`varchar(255)DEFAULT NULL,`siteurl`varchar(255)DEFAULT NULL,`sitewapurl`varchar(255)DEFAULT NULL,`companyname`varchar(255)DEFAULT NULL,`companyaddress`varchar(255)DEFAULT NULL,`companymappoint`varchar(255)DEFAULT NULL,`companypostcode`varchar(255)DEFAULT NULL,`companycontact`varchar(255)DEFAULT NULL,`companytel`varchar(255)DEFAULT NULL,`companymobile`varchar(255)DEFAULT NULL,`companyfax`varchar(255)DEFAULT NULL,`companyemail`varchar(255)DEFAULT NULL,`companyicp`varchar(255)DEFAULT NULL,`statisticalcode`longtext,`copyright`longtext,`sitekeys`longtext,`sitedesc`longtext,`isdefault`int(11)DEFAULT NULL,`qq`varchar(255)DEFAULT NULL,`weixin`varchar(255)DEFAULT NULL,PRIMARY KEY(`lid`))ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2");
		$link->query("INSERT INTO `zzz_language` (`lid`,`l_name`, `l_path`, `l_order`, `l_onoff`, `l_alias`, `pctemplate`, `waptemplate`, `pchtmlpath`, `waphtmlpath`, `sitetitle`, `additiontitle`, `sitepclogo`, `sitewaplogo`, `siteurl`, `sitewapurl`, `companyname`, `companyaddress`, `companymappoint`, `companypostcode`, `companycontact`, `companytel`, `companymobile`, `companyfax`, `companyemail`, `companyicp`, `statisticalcode`, `copyright`, `sitekeys`, `sitedesc`, `isdefault`, `qq`, `weixin`) VALUES
( 1,'中文', '', 0, 1, 'ch', 'cn2016/', 'cn2016/', 'html/', 'html/', 'zzzcms-PHP建站系统', 'zzzcms', '/images/logo.png', '/images/waplogo.png', 'http://localhost/', 'http://localhost/wap', '公司名', '', '116.404309,39.905589', '', '管理员', '88888888', '13888888888', '', '', '', '', '版权所有 ?2015-2017 zzcms.com', '', '', 1, '', '')");
		$link->query("CREATE TABLE IF NOT EXISTS`zzz_user`(`uid`int(11)NOT NULL AUTO_INCREMENT,`u_gid`int(11)DEFAULT NULL,`u_lid`int(11)DEFAULT NULL,`u_onoff`int(1)DEFAULT NULL,`u_order`int(11)DEFAULT NULL,`sex`varchar(255)DEFAULT NULL,`username`varchar(255)DEFAULT NULL,`password`varchar(255)DEFAULT NULL,`question`varchar(255)DEFAULT NULL,`answer`varchar(255)DEFAULT NULL,`regtime`datetime DEFAULT NULL,`truename`varchar(255)DEFAULT NULL,`face`varchar(255)DEFAULT NULL,`province`varchar(255)DEFAULT NULL,`city`varchar(255)DEFAULT NULL,`district`varchar(255)DEFAULT NULL,`address`varchar(255)DEFAULT NULL,`post`varchar(255)DEFAULT NULL,`tel`varchar(255)DEFAULT NULL,`mobile`varchar(255)DEFAULT NULL,`email`varchar(255)DEFAULT NULL,`qq`varchar(255)DEFAULT NULL,`u_desc`longtext,`adminrand`varchar(255)DEFAULT NULL,`lastlogintime`varchar(255)DEFAULT NULL,`lastloginip`varchar(255)DEFAULT NULL,`logincount`int(11)DEFAULT NULL,`sysinfo`varchar(255)DEFAULT NULL,`points`int(11)DEFAULT NULL,`balance`int(11)DEFAULT NULL,PRIMARY KEY(`uid`))ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2");
		$link->query("INSERT INTO`zzz_user`(`uid`,`u_gid`,`u_lid`,`u_onoff`,`u_order`,`sex`,`username`,`password`,`question`,`answer`,`regtime`,`truename`,`face`,`province`,`city`,`district`,`address`,`post`,`tel`,`mobile`,`email`,`qq`,`u_desc`,`adminrand`,`lastlogintime`,`lastloginip`,`logincount`,`sysinfo`,`points`,`balance`)VALUES
(1,1,1,1,0,'男','admin','49ba59abbe56e057','2342','452435345','2018-04-13 13:51:19','创始人','face1.png','','','','','','','','','','','','','',0,'',0,0)");
		$link =NULL;
		break;
		default:
		back ('很抱歉，请选择正确的数据库类型');
	}
	if ($adminpath!=$oldpath){		
		if(!rename(SITE_DIR.$oldpath,SITE_DIR.$adminpath)){
			jsgo('文件夹权限不足，无法完成安装！','?act=step2');
		}
	}
	save_config(array('isinstall'=>'1'));
	replace_file(SITE_DIR.'js/zzzcms.js','SITE_PATH',$sitepath);
	phpgo('?act=step3');
}
function progress(){
	$db=conf('db');
	if($db['type']=='mysql'){
		$start=getform("start","post","num",0);
		$to=getform("to","post","num",100);	
		$sql = file_get_contents('data/mysql.data');
		$data = explode(';', $sql);
		$count=count($data);
		$d=$_SERVER['db'];
		 foreach ($data as $value) {
			if ($value){
				$d->exec($value);
			}
		}
		echo tojson(array('count'=>$count,"start"=>0,"to"=>$count));
	}
	else{
		echo tojson(array('count'=>1,"start"=>1,"to"=>1));
	}
}
function testdata(){
		$start=getform("start","post","num",0);
		$to=getform("to","post","num",100);	
		$db=conf('db');
		if($db['type']=='access'){
			$sql = file_get_contents('data/test_gbk.data');	
		}else{
			$sql = file_get_contents('data/test_utf8.data');	
		}
		$data = explode(';'.PHP_EOL, $sql);
		$count=count($data);
		$d=$_SERVER['db'];
		 foreach ($data as $value) {
			if ($value){
				if(SITE_PATH!='/') $value=str_replace('/upload/',SITE_PATH.'upload/',$value);
				$d->exec($value);
			}
		}
		echo tojson(array('count'=>$count,"start"=>0,"to"=>$count));
	}