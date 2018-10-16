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
class company_order_controller extends common
{
	function index_action(){
		if($_GET["news_search"]){
			extract($_GET);
			if ($typezf!=""){
				$where .=" `order_type`='$typezf'";
				$urlarr["typezf"]=$typezf;
			}else{
				$where .="1";
			}
			if ($typedd!=""){
				$where .=" and `type`=$typedd";
				$urlarr["typedd"]=$typedd;
			}
			if ($keyword!=""){
				if ($typeca=='2'){
				    $where .=" and `order_remark` like '%".$keyword."%'";
			     }elseif($typeca=='1'){
				     $where .=" and `order_id` like '%".$keyword."%'";
			     }
			     $urlarr["typeca"]=$typeca;
			     $urlarr["keyword"]=$_GET["keyword"];
			}
			 $urlarr["news_search"]=$_GET["news_search"];
		}else{
			$where=1;
		}
		if($_GET["order_state"]!=""){
	           $where.=" and `order_state`='".$_GET["order_state"]."'";
				$urlarr["order_state"]=$_GET["order_state"];
		    }
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET["m"],$urlarr);
		$rows=$this->get_page("company_order",$where,$pageurl,$this->config["sy_listnum"]);
		include (APP_PATH."/data/db.data.php");
		if(is_array($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]["order_state_n"]=$arr_data["paystate"][$v["order_state"]];
				$rows[$k]["order_type_n"]=$arr_data["pay"][$v["order_type"]];
 			}
		}
        $this->yunset("get_type", $_GET);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_company_order'));
	}
	function edit_action(){
		$id=(int)$_GET["id"];
		$row=$this->obj->DB_select_alls('member',"company_order","b.`id`='".$id."' and a.`uid`=b.`uid`","a.username,b.*");
		$this->yunset("row",$row[0]);
		$this->yuntpl(array('admin/admin_company_order_edit'));
	}
	function save_action(){
		$r_id=$this->obj->DB_update_all("company_order","`order_price`='".$_POST['order_price']."',`order_remark`='".$_POST['order_remark']."'","id='".$_POST['id']."'");
		isset($r_id)?$this->obj->ACT_layer_msg("充值记录(ID:".$_POST['id'].")修改成功！",9,"index.php?m=company_order",2,1):$this->obj->ACT_layer_msg("修改失败,请销后再试！",8,"index.php?m=company_order");
	}
	function setpay_action(){
		$del=(int)$_GET["id"];
		$row=$this->obj->DB_select_once("company_order","`id`='$del'");
		if($row["order_state"]==1||$row["order_state"]==3){
			$nid=$this->upuser_statis($row);
			isset($nid)?$this->layer_msg("充值记录(ID:".$del.")确认成功！",9):$this->layer_msg("确认失败,请销后再试！",8);
		}else{
			$this->layer_msg("订单已确认，请勿重复操作！",8);
		}
	}
	function del_action(){
		$this->check_token();
	    if($_GET["del"]){
	    	$del=$_GET["del"];
	    	if(is_array($del)){
				$this->obj->DB_delete_all("company_order","`id` in(".@implode(',',$del).")","");
				$this->layer_msg( "充值记录(ID:".@implode(',',$del).")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET["id"])){
			$result=$this->obj->DB_delete_all("company_order","`id`='".$_GET["id"]."'" );
			isset($result)?$this->layer_msg('充值记录(ID:'.$_GET["id"].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>