<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
//---------------------------------------------------------
//财付通即时到帐支付请求示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require_once ("classes/PayRequestHandler.class.php");
require_once ("tenpay_data.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
if(!is_numeric($_POST['dingdan']))
{
	die;
}

$userid=(int)$_COOKIE['uid'];
$_POST['is_invoice']=(int)$_POST['is_invoice'];
$_POST['balance']=(int)$_POST['balance'];
$member_sql=$db->query("SELECT * FROM `".$db_config["def"]."member` WHERE `uid`='".$userid."' limit 1");
$member=mysql_fetch_array($member_sql);
if($member['username'] != $_COOKIE['username'] || $member['usertype'] != $_COOKIE['usertype']||md5($member['username'].$member['password'].$member['salt'])!=$_COOKIE['shell']){
	echo '登录信息验证错误，请重新登录！';die;
}
$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='$_POST[dingdan]'");
$row=mysql_fetch_array($sql);
if($_POST['balance']&&$userid){
	$c_sql=$db->query("select `pay` from `".$db_config["def"]."company_statis` where `uid`='".$userid."'");
	$company_statis=mysql_fetch_array($c_sql);
	if($company_statis['pay']>=$row['order_price']){
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`=`pay`-'".$row['order_price']."' where `uid`='".$userid."'");
		mysql_fetch_array($up_sql);

		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`='0'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order);
		$price=$row['order_price'];
	}else{
		$price=$company_statis['pay'];
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`='0' where `uid`='".$userid."'");
		$up_sql_status=mysql_fetch_array($up_sql);

		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`=`order_price`-'".$price."'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order);
	}
	$insert_company_pay=$db->query("insert into `".$db_config["def"]."company_pay`(order_id,order_price,pay_time,pay_state,com_id,pay_remark,type) values('".$row['order_id']."','-".$price."','".time()."','2','".$userid."','".$row['order_remark']."','2')");
	mysql_fetch_array($insert_company_pay);
	$new_sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$row['order_id']."'");
	$row=mysql_fetch_array($new_sql);
}
/* 商户号 */
$bargainor_id = $tenpaydata[sy_tenpayid];

/* 密钥 */
$key = $tenpaydata[sy_tenpaycode];

/* 返回处理地址 */
$return_url = $tenpaydata[sy_weburl]."/api/tenpay/return_url.php";

//date_default_timezone_set(PRC);
$strDate = date("Ymd");
$strTime = date("His");

//4位随机数
$randNum = rand(1000, 9999);

$attach=$_POST[pay_type];

//10位序列号,可以自行调整。
$strReq = $strTime . $randNum;

/* 商家订单号,长度若超过32位，取前32位。财付通只记录商家订单号，不保证唯一。 */
$sp_billno = $_POST[dingdan];

/* 财付通交易单号，规则为：10位商户号+8位时间（YYYYmmdd)+10位流水号 */
$transaction_id =trim($bargainor_id.$strDate.$strReq);

/* 商品价格（包含运费），以分为单位 */
$total_fee = $row[order_price]*100;
//$total_fee = 1;

/* 商品名称 */
$desc = "订单号：" . $transaction_id;

/* 创建支付请求对象 */
$reqHandler = new PayRequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
//----------------------------------------
//设置支付参数
//----------------------------------------
$reqHandler->setParameter("bargainor_id", $bargainor_id);			//商户号
$reqHandler->setParameter("transaction_id", $transaction_id);		//财付通交易单号
$reqHandler->setParameter("sp_billno", $sp_billno);					//商户订单号
$reqHandler->setParameter("total_fee", $total_fee);					//商品总金额,以分为单位
$reqHandler->setParameter("return_url", $return_url);				//返回处理地址
$reqHandler->setParameter("desc", "订单号：" . $transaction_id);	    //商品名称
$reqHandler->setParameter("attach", $attach);			        	//自定义参数
//用户ip,测试环境时不要加这个ip参数，正式环境再加此参数
//$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);



//请求的URL
$reqUrl = $reqHandler->getRequestURL();


//debug信息
//$debugInfo = $reqHandler->getDebugInfo();

//echo "<br/>" . $reqUrl . "<br/>";
//echo "<br/>" . $debugInfo . "<br/>";

//重定向到财付通支付
//$reqHandler->doSend();
Header("Location:$reqUrl");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gbk">
	<title>财付通即时到帐程序</title>
</head>
<body>
<script>//location.href='<?php echo $reqUrl;?>';</script>
</body>
</html>
