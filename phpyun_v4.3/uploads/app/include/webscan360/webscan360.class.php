<?php

require_once 'lib/webscan360_db.class.php';
require_once 'lib/webscan360_http.class.php';
class webscan360 {
    private $webscan360_getkey_url = "http://webscan.360.cn/mmapi/get-key";
	private $webscan360_verifydomain_url = "http://webscan.360.cn/mmapi/verify-domain";
	private $webscan360_protect_url = "http://webscan.360.cn/mmapi/report/key/";
	private $webscan360_webshell_url = "http://webscan.360.cn/mmapi/scan/key/";
	private $webscan360_noadmin_url = "http://webscan.360.cn/mmapi/no-admin/infocode/";
	private $webscan360_config = '';

	public function __construct(){
		define("WEBSCAN360" , true);
		$webscan360_config = include('webscan360_config.php');
		$this->webscan360_config = $webscan360_config;
	}

	public function getProtectUrl( ){
		$ret_url = null;
		$webscan360db = new Webscan360_db();
		if(empty($webscan360db->tablename)){
			return $this->webscan360_noadmin_url . "501@200";
		}
		if(empty($this->webscan360_config['MID'])){
			return $this->webscan360_noadmin_url . "502@200";
		}
		$result = $webscan360db->rec_getRow(array('var'=>'key'));
		
		if(!empty($result) && !empty($result['value']) && $result['state'] == 1){
			$result_p = $webscan360db->rec_getRow(array('var'=>'pkey'));
			if(!empty($result_p) && !empty($result_p['value'])){
				$ret_url = $this->webscan360_protect_url.$result_p['value'];
				
			}
		}else{
			$result_verify = $this->verifyDomain();
			if(!empty($result_verify) && $result_verify['infocode'] == "111"){
				$this->addWebscankey($result_verify);
				if(!empty($result_verify['pkey'])){
					$ret_url = $this->webscan360_protect_url.$result_verify['pkey'];
				}
			}else{
				$infocode = $result_verify['infocode'].'@'.$result_verify['httpcode'];
				$ret_url = $this->webscan360_noadmin_url . $infocode;
			}
		}
		if(!empty($ret_url)){
			$ret_url = $ret_url . '/?from='.$this->webscan360_config['MID'];
		}
		return $ret_url;	
		
	}

	public function getWebshellUrl( ){
		$ret_url = null;
		$webscan360db = new Webscan360_db();
		if(empty($webscan360db->tablename)){
			return $this->webscan360_noadmin_url . "501@200";
		}
		if(empty($this->webscan360_config['MID'])){
			return $this->webscan360_noadmin_url . "502@200";
		}
		$result = $webscan360db->rec_getRow(array('var'=>'key'));
		
		if(!empty($result) && !empty($result['value']) && $result['state'] == 1){
			$result_p = $webscan360db->rec_getRow(array('var'=>'skey'));
			if(!empty($result_p) && !empty($result_p['value'])){
				$ret_url = $this->webscan360_webshell_url.$result_p['value'];
				
			}
		}else{
			$result_verify = $this->verifyDomain();
			if(!empty($result_verify) && $result_verify['infocode'] == "111"){
				$this->addWebscankey($result_verify);
				if(!empty($result_verify['skey'])){
					$ret_url = $this->webscan360_webshell_url.$result_verify['skey'];
				}
			}else{
				$infocode = $result_verify['infocode'].'@'.$result_verify['httpcode'];
				$ret_url = $this->webscan360_noadmin_url . $infocode;
			}
		}
		if(!empty($ret_url)){
			$ret_url = $ret_url . '/?from='.$this->webscan360_config['MID'];
		}
		return $ret_url;	
		
	}

	private function addWebscankey($result) {
		$webscan360db = new Webscan360_db();
		$webscan360db->rec_update ( array ('state' => 1 ), array ('var' => 'key' ) );

		if (! empty ( $result ['pkey'] )) {
			$res_p = $webscan360db->rec_getRow( array ('var' => 'pkey' ) );
			if (empty ( $res_p )) {
				$webscan360db->rec_insert( array ('var' => 'pkey', 'value' => $result ['pkey'] ) );
			} else {
				$webscan360db->rec_update( array ('value' => $result ['pkey'] ), array ('var' => 'pkey' ) );
			}
		}

		if (! empty ( $result ['skey'] )) {
			$res_s = $webscan360db->rec_getRow( array ('var' => 'skey' ) );
			if (empty ( $res_s )) {
				$webscan360db->rec_insert( array ('var' => 'skey', 'value' => $result ['skey'] ) );
			} else {
				$webscan360db->rec_update( array ('value' => $result ['skey'] ), array ('var' => 'skey' ) );
			}
		}
	}

	private function verifyDomain() {
		$webscan_config = $this->webscan360_config;
		if(!empty($webscan_config)){
			$site_url = $webscan_config['SITE_URL'];
			if(!empty($site_url)){
				$site_url ="http://".str_replace("http://","",strtolower($site_url));
			}
		}
		if(empty($site_url)){
			$site_url = $_SERVER ['HTTP_HOST'];
		}
		$result = array ('infocode' => "no", 'msg' => "" );
		
		$model = new webscan360_http( );
		$ret = $model->http_request ( $this->webscan360_getkey_url, array ('host' => $site_url , 'mid'=>$webscan_config['MID'] ) );
		$httpcode = $ret ['httpcode'];
		$response = $ret ['response'];
		$response = json_decode ( $response, true );
		$webscan360db = new Webscan360_db();
		if (! empty ( $ret ) && ! empty ( $response ) && $httpcode == 200 && $response ['infocode'] == "111" && ! empty ( $response ['key'] )) {
			$key = $response ['key'];
			$res_key = $webscan360db->rec_getRow( array ('var' => 'key' ) );
			if (! empty ( $res_key )) {
				$op_ret = $webscan360db->rec_update( array ('value' => $key ), array ('var' => 'key' ) );
			} else {
				$op_ret = $webscan360db->rec_insert ( array ('var' => 'key', 'value' => $key ) );
			}
			if ($op_ret) {
				$ret_verityDomain = $model->http_request ( $this->webscan360_verifydomain_url, array ('key' => $key, 'host' => $site_url, 'mid'=>$webscan_config['MID'] ) );
				$httpcode_verityDomain = $ret_verityDomain ['httpcode'];
				$response_verityDomain = $ret_verityDomain ['response'];
				if (! empty ( $ret_verityDomain ) && ! empty ( $response_verityDomain ) && $httpcode_verityDomain == 200) {
					$response_verityDomain_array = json_decode ( $response_verityDomain, true );
					$result = $response_verityDomain_array;
				} else {
					$result = array ('infocode' => "203", 'msg' => "not verify host from 360webscan", 'httpcode' => $httpcode_verityDomain );
				}
			} else {
				$result = array ('infocode' => "202", 'msg' => "not insert key" );
			}
		} else {
			if ($response['infocode'] == "300"||$response['infocode'] == "106") {
				$result = $response;
			} else {
				$result = array ('infocode' => "201", 'msg' => "not get key from 360webscan", 'httpcode' => $httpcode );
			}
		}
		if (! empty ( $result )) {
			$webscan360db->rec_insert( array ('var' => 'log_verify', 'value' => json_encode ( $result ) ) );
		}
		return $result;
	}
}