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
require_once("class/alipay_notify.php");
require_once("alipay_config.php");

require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);

$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->notify_verify(); 

if($verify_result) {
	if(!is_numeric($_POST['out_trade_no']))
	{
		die;
	}
 
    $dingdan           = $_POST['out_trade_no'];
    $total             = $_POST['total_fee'];
	$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$dingdan."'");
    $row=mysql_fetch_array($sql);
    $sOld_trade_status =$row['order_state'];	
  
    if($_POST['trade_status'] == 'TRADE_FINISHED' ||$_POST['trade_status'] == 'TRADE_SUCCESS') {
     
		if($sOld_trade_status=="1")
		{
			if($row['type']=="1"){
				$select=$db->query("select * from `".$db_config["def"]."company_rating` where `id`='".$row["rating"]."'");
				$comuid=mysql_fetch_array($select);
				$value="`rating`='".$comuid["id"]."',";
				$value.="`rating_name`='".$comuid["name"]."',";
				if($statis["vip_time"]==0){
					$viptime=mktime()+$comuid["service_time"]*86400;
				}else{
					$viptime=$comuid["service_time"]*86400;
				}
				$value.="`vip_time`=`vip_time`+'$viptime',";
				$value.="`job_num`=`job_num`+".$comuid["job_num"].",";
				$value.="`down_resume`=`down_resume`+".$comuid["resume"].",";
				$value.="`invite_resume`=`invite_resume`+".$comuid["interview"].",";
				$value.="`editjob_num`=`editjob_num`+".$comuid["editjob_num"].",";
				$value.="`breakjob_num`=`breakjob_num`+".$comuid["breakjob_num"];
				mysql_query("update `".$db_config["def"]."company_statis` SET `all_pay`=`all_pay`+".$comuid["service_price"].",".$value." where `uid`='".$row["uid"]."'");

			}elseif($row["type"]=="2"){

				mysql_query("update `".$db_config["def"]."company_statis` SET `integral`=`integral`+".$row["integral"]." where `uid`='".$row["uid"]."'");
			}
			@file_get_contents($alipaydata["sy_weburl"]."/index.php?m=ajax&c=paypost&dingdan=".$dingdan);
			mysql_query("update `".$db_config["def"]."company_order` set order_state='2' where `order_id`='$row[order_id]'");

			if($sOld_trade_status < 1) {
	
			}
			echo "success";

		}
	}

    else {
        echo "success";	
    }
}
else {
  
    echo "fail";

}
?>