<?php

require_once("class/alipay_notify.php");
require_once("alipay_config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);



$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);

$verify_result = $alipay->return_verify();

if($verify_result) {

    $dingdan           = $_GET['out_trade_no'];
    $total_fee         = $_GET['total_fee'];
    $sOld_trade_status = "0";


    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {

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

        if ($sOld_trade_status < 1) {


        }
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
}
else {


}
?>
<html>
    <head>
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
                <td align="center" class="font_title" colspan="2">通知返回</td>
            </tr>
            <tr>
                <td class="font_content" align="right">支付宝交易号：</td>
                <td class="font_content" align="left"><?php echo $_GET['trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">订单号：</td>
                <td class="font_content" align="left"><?php echo $_GET['out_trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">付款总金额：</td>
                <td class="font_content" align="left"><?php echo $_GET['total_fee']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">商品标题：</td>
                <td class="font_content" align="left"><?php echo $_GET['subject']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">商品描述：</td>
                <td class="font_content" align="left"><?php echo $_GET['body']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">买家账号：</td>
                <td class="font_content" align="left"><?php echo $_GET['buyer_email']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">交易状态：</td>
                <td class="font_content" align="left"><?php echo $_GET['trade_status']; ?></td>
            </tr>
        </table>
 <script src="../../commanage.php?action=dingdan&pay=alipay&dingdan=<?php echo $_GET['out_trade_no']; ?>&state=<?php echo $_GET['trade_status']; ?>"></script>
    </body>
</html>