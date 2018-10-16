<?php

error_reporting(0);
require_once("alipay_config.php");
require_once("class/alipay_service.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");

require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
if(!is_numeric($_POST['dingdan'])){die;}
$userid=(int)$_COOKIE['uid'];
$_POST['is_invoice']=(int)$_POST['is_invoice'];
$_POST['balance']=(int)$_POST['balance'];
$member_sql=$db->query("SELECT * FROM `".$db_config["def"]."member` WHERE `uid`='".$userid."' limit 1");
$member=mysql_fetch_array($member_sql);
if($member['username'] != $_COOKIE['username'] || $member['usertype'] != $_COOKIE['usertype']||md5($member['username'].$member['password'].$member['salt'])!=$_COOKIE['shell']){
	echo '登录信息验证错误，请重新登录！';die;
}

$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$_POST['dingdan']."'");
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

$out_trade_no = $_POST['dingdan'];
$subject      = $_POST['dingdan'];
$body         = $row['order_remark'];
$total_fee    = $row['order_price'];

$pay_mode	  = $_POST['pay_bank'];

if ($pay_mode == "directPay") {
	$paymethod    = "directPay";
	$defaultbank  = "";
}
else {
	$paymethod    = "bankPay";
	$defaultbank  = $pay_mode;
}

$encrypt_key  = '';
$exter_invoke_ip = '';
if($antiphishing == 1){
    $encrypt_key = query_timestamp($partner);
	$exter_invoke_ip = '';
}

$extra_common_param =$_POST['pay_type'];
$buyer_email		= '';


$parameter = array(
        "service"         => "create_direct_pay_by_user",
        "payment_type"    => "1",

        "partner"         => $partner,
        "seller_email"    => $seller_email,
        "return_url"      => $return_url,
        "notify_url"      => $notify_url,
        "_input_charset"  => $_input_charset,
        "show_url"        => $show_url,

        "out_trade_no"    => $out_trade_no,
        "subject"         => $subject,
        "body"            => $body,
        "total_fee"       => $total_fee,

        "paymethod"	      => $paymethod,
        "defaultbank"	  => $defaultbank,

        "anti_phishing_key"=> $encrypt_key,
		"exter_invoke_ip" => $exter_invoke_ip,

		"buyer_email"	 => $buyer_email,
        "extra_common_param" => $extra_common_param
);

$alipay = new alipay_service($parameter,$security_code,$sign_type);


$url = $alipay->create_url();
$sHtmlText = "<a href=".$url."><img border='0' src='images/alipay.gif' /></a>";
echo "<script>window.location =\"$url\";</script>";


?>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
        <title>支付宝即时支付</title>
        <style type="text/css">
            .font_content{
                font-family:"宋体";
                font-size:14px;
                color:#FF6600;
            }
            .font_title{
                font-family:"宋体";
                font-size:16px;
                color:#FF0000;
                font-weight:bold;
            }
            table{
                border: 1px solid #CCCCCC;
            }
        </style>
    </head>
    <body>

        <table align="center" width="350" cellpadding="5" cellspacing="0">
            <tr>
                <td align="center" class="font_title" colspan="2">订单确认</td>
            </tr>
            <tr>
                <td class="font_content" align="right">订单号：</td>
                <td class="font_content" align="left"><?php echo $out_trade_no; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">付款总金额：</td>
                <td class="font_content" align="left"><?php echo $total_fee; ?></td>
            </tr>
            <tr>
                <td align="center" colspan="2"><?php echo $sHtmlText; ?></td>
            </tr>
        </table>
    </body>
</html>
