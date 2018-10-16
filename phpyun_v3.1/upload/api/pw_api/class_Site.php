<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This PHPYun.Rencai System Powered by PHPYun.com
 */
!defined('P_W') && exit('Forbidden');
//api mode 9

class Site {

	var $base;
	var $db;

	function Site($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function connect() {
		return new ApiResponse(1);
	}
}
?>