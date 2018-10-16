<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
//---------------------------------------------------------
//财付通即时到帐支付应答（处理回调）示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require_once ("./classes/PayResponseHandler.class.php");
require_once ("tenpay_data.php");

//引入本站数据
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);

/* 密钥 */
$key =$tenpay[sy_tenpaycode];

/* 创建支付应答对象 */
$resHandler = new PayResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {

	//交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");

	//本站单号
	$sp_billno = $resHandler->getParameter("sp_billno");

	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");

	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");
	//类型
	$attach = $resHandler->getParameter("attach");

	if( "0" == $pay_result ) {

		//------------------------------
		//处理业务开始
		//------------------------------

		//注意交易单不要重复处理
		//注意判断返回金额
//处理本站信息开始
	if(!is_numeric($sp_billno))
	{
		die;
	}
	$select=$db->query("select  * from `".$db_config[def]."company_order` where `order_id`='$dingdan'");
	$order=mysql_fetch_array($select);
	if($order['order_state']!='2'){
		if($order['type']=='1'&&$order['rating']){
			$select_rating=$db->query("select `* from `".$db_config[def]."company_rating` where `id`='".$order['rating']."'");
			$row=mysql_fetch_array($select_rating);
			$value="`rating`='".$row['id']."',";
			$value.="`rating_name`='".$row['name']."',";
			if($row['type']=="2"){
				if($statis['vip_etime']==0){
					$viptime=mktime()+$row['service_time']*86400;
				}else{
					$viptime=$row['service_time']*86400;
				}
				$value.="`vip_etime`=`vip_etime`+'".$viptime."'";
			}else{
				$value.="`job_num`=`job_num`+".$row['job_num'].",";
				$value.="`down_resume`=`down_resume`+".$row['resume'].",";
				$value.="`invite_resume`=`invite_resume`+".$row['interview'].",";
				$value.="`editjob_num`=`editjob_num`+".$row['editjob_num'].",";
				$value.="`breakjob_num`=`breakjob_num`+".$row['breakjob_num']."";
			}
			mysql_query("update `".$db_config[def]."company_statis` set ".$value." where `uid`='".$order["uid"]."'");
		}else if($order['type']=='2'){//充值积分
			$num=ceil($order['order_price']*$db_config['integral_proportion']);
			mysql_query("update `".$db_config[def]."company_statis` set `integral`=`integral`+'".$num."' where `uid`='".$order["uid"]."'");
		}else if($order['type']=='3'){//银行转账
			mysql_query("update `".$db_config[def]."company_statis` set `pay`=`pay`+".$order["order_price"].",`all_pay`=`all_pay`+".$order["order_price"]."' where `uid`='".$order["uid"]."'");
		}
		mysql_query("update `".$db_config[def]."company_order` set `order_state`='2' where `id`='".$order['id']."'");
	}
	@file_get_contents($tenpaydata["sy_weburl"]."/index.php?m=ajax&c=paypost&dingdan=".$sp_billno);
	mysql_query("update `".$db_config["def"]."company_order` set order_state='2' where `order_id`='$sp_billno'");
//处理本站信息结束




		//------------------------------
		//处理业务完毕
		//------------------------------

		//调用doShow, 打印meta值跟js代码,告诉财付通处理成功,并在用户浏览器显示$show页面.
		$show = $tenpay[sy_weburl]."/app/tenpay/show.php";
		$resHandler->doShow($show);

	} else {
		//当做不成功处理
		echo "<br/>" . "支付失败" . "<br/>";
	}

} else {
	echo "<br/>" . "认证签名失败" . "<br/>";
}

//echo $resHandler->getDebugInfo();

?>