<?php
/**
 * @brief 升级更新控制器
 */
class Update extends IController
{
	/**
	 * @brief iwebshop5.3 版本升级更新
	 */
	public function index()
	{
		set_time_limit(0);

		$sql = array(
		    "ALTER TABLE `{pre}goods` CHANGE `name` `name` VARCHAR(255) NOT NULL COMMENT '商品名称'",
		    "ALTER TABLE `{pre}oauth_user` ADD `openid` VARCHAR(80) NOT NULL DEFAULT '' COMMENT 'openid参数只针对微信'",

		    "ALTER TABLE `{pre}refundment_doc` ADD `user_freight_id` int(11) unsigned default NULL COMMENT '用户发货时货运公司ID'",
            "ALTER TABLE `{pre}refundment_doc` ADD `user_delivery_code` varchar(30) default NULL COMMENT '用户发货时快递单号'",
            "ALTER TABLE `{pre}refundment_doc` ADD `user_send_time` datetime default NULL COMMENT '发货时间'",

            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货申请单列表', 'order@exchange_list', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货申请单详情', 'order@exchange_doc_show', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货申请单删除', 'order@exchange_doc_del', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货申请单修改', 'order@exchange_doc_show_save', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货单列表', 'order@order_exchange_list', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货单删除', 'order@order_exchange_del', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货单回收站', 'order@exchange_recycle_list,order@exchange_recycle_restore,order@exchange_recycle_del', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]换货单详情', 'order@exchange_show', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修申请单列表', 'order@fix_list', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修申请单详情', 'order@fix_doc_show', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修申请单删除', 'order@fix_doc_del', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修申请单修改', 'order@fix_doc_show_save', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修单列表', 'order@order_fix_list', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修单删除', 'order@order_fix_del', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修单回收站', 'order@fix_recycle_list,order@fix_recycle_restore,order@fix_recycle_del', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]维修单详情', 'order@fix_show', 0);",
            "INSERT INTO `{pre}right` VALUES (NULL, '[订单]配货单更新', 'order@delivery_doc_update', 0);",

            "DROP TABLE IF EXISTS `{pre}exchange_doc`;",
            "CREATE TABLE `{pre}exchange_doc` (
              `id` int(11) unsigned NOT NULL auto_increment,
              `order_no` varchar(20) NOT NULL COMMENT '订单号',
              `order_id` int(11) unsigned NOT NULL COMMENT '订单ID',
              `user_id` int(11) unsigned default 0 COMMENT '用户ID',
              `time` datetime default NULL COMMENT '时间',
              `admin_id` int(11) unsigned default NULL COMMENT '管理员ID',
              `status` tinyint(1) NOT NULL default '0' COMMENT '状态,0:申请中 1:已拒绝 2:已完成 3:等待买家发货 4:等待商家确认',
              `content` text COMMENT '申请原因',
              `dispose_time` datetime default NULL COMMENT '处理时间',
              `dispose_idea` text COMMENT '处理意见',
              `if_del` tinyint(1) NOT NULL default '0' COMMENT '0:未删除 1:已删除',
              `order_goods_id` text COMMENT '订单与商品关联ID集合',
              `seller_id` int(11) unsigned NOT NULL default '0' COMMENT '商家ID',
              `img_list` text COMMENT '图片',
              `user_freight_id` int(11) unsigned default NULL COMMENT '用户发货时货运公司ID',
              `user_delivery_code` varchar(30) default NULL COMMENT '用户发货时快递单号',
              `user_send_time` datetime default NULL COMMENT '发货时间',
              `seller_freight_id` int(11) unsigned default NULL COMMENT '商家发货时货运公司ID',
              `seller_delivery_code` varchar(30) default NULL COMMENT '商家发货时快递单号',
              `seller_send_time` datetime default NULL COMMENT '发货时间',
              `_hash` int(11) unsigned NOT NULL COMMENT '预留散列字段',
              PRIMARY KEY  (`id`,`_hash`),
              index (`order_id`),
              index (`seller_id`),
              index (`if_del`),
              index (`user_id`),
              index (`status`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='售后换货单';",

            "DROP TABLE IF EXISTS `{pre}fix_doc`;",
            "CREATE TABLE `{pre}fix_doc` (
              `id` int(11) unsigned NOT NULL auto_increment,
              `order_no` varchar(20) NOT NULL COMMENT '订单号',
              `order_id` int(11) unsigned NOT NULL COMMENT '订单ID',
              `user_id` int(11) unsigned default 0 COMMENT '用户ID',
              `time` datetime default NULL COMMENT '时间',
              `admin_id` int(11) unsigned default NULL COMMENT '管理员ID',
              `status` tinyint(1) NOT NULL default '0' COMMENT '状态,0:申请中 1:已拒绝 2:已完成 3:等待买家发货 4:等待商家确认',
              `content` text COMMENT '申请原因',
              `dispose_time` datetime default NULL COMMENT '处理时间',
              `dispose_idea` text COMMENT '处理意见',
              `if_del` tinyint(1) NOT NULL default '0' COMMENT '0:未删除 1:已删除',
              `order_goods_id` text COMMENT '订单与商品关联ID集合',
              `seller_id` int(11) unsigned NOT NULL default '0' COMMENT '商家ID',
              `img_list` text COMMENT '图片',
              `user_freight_id` int(11) unsigned default NULL COMMENT '用户发货时货运公司ID',
              `user_delivery_code` varchar(30) default NULL COMMENT '用户发货时快递单号',
              `user_send_time` datetime default NULL COMMENT '发货时间',
              `seller_freight_id` int(11) unsigned default NULL COMMENT '商家发货时货运公司ID',
              `seller_delivery_code` varchar(30) default NULL COMMENT '商家发货时快递单号',
              `seller_send_time` datetime default NULL COMMENT '发货时间',
              `_hash` int(11) unsigned NOT NULL COMMENT '预留散列字段',
              PRIMARY KEY  (`id`,`_hash`),
              index (`order_id`),
              index (`seller_id`),
              index (`if_del`),
              index (`user_id`),
              index (`status`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='售后维修单';",
		);

		foreach($sql as $key => $val)
		{
		    IDBFactory::getDB()->query( $this->_c($val) );
		}

        //更新微信插件，增加消息模板配置
		$pluginDB = new IModel('plugin');
		$pluginRow= $pluginDB->getObj('class_name = "wechat"');
		if($pluginRow)
		{
		    if(isset($pluginRow['config_param']) && stripos($pluginRow['config_param'],'wechat_tempalteMsg') === false)
		    {
    		    $config_param = JSON::decode($pluginRow['config_param']);
    		    $config_param['wechat_tempalteMsg'] = 0;
    		    $pluginDB->setData(array('config_param' => JSON::encode($config_param)));
    		    $pluginDB->update($pluginRow['id']);
		    }

    		//商户openid关系表
    	    $sellerOpenidRelationDB = new IModel('seller_openid_relation');
    	    if(!$sellerOpenidRelationDB->exists())
    	    {
    	        $data = array(
    	            'comment' => '用户的openid关系表',
    	            'column'  => array(
    	                'seller_id' => array("type" => "int(11) unsigned","comment" => "商家ID"),
    	                'openid' => array("type" => "varchar(80)","comment" => "微信openid"),
    	                'datetime' => array("type" => "datetime","comment" => "绑定时间"),
    	            ),
    	            'index' => array("primary" => "seller_id"),
    	        );
        	    $sellerOpenidRelationDB->setData($data);
        		unset($data);
        		$sellerOpenidRelationDB->createTable();
    	    }

    		//模板消息表
    	    $wechatTemplateMsgDB = new IModel('wechat_template_message');
    	    if(!$wechatTemplateMsgDB->exists())
    	    {
    	        $data = array(
    	            'comment' => '模板消息ID关系表',
    	            'column'  => array(
    	                'short_id' => array("type" => "varchar(120)","comment" => "模板短ID"),
    	                'template_id' => array("type" => "varchar(120)","comment" => "模板长ID"),
    	            ),
    	            'index' => array("primary" => "short_id"),
    	        );
        	    $wechatTemplateMsgDB->setData($data);
        		unset($data);
        		$wechatTemplateMsgDB->createTable();
    	    }
		}

        //清空runtime缓存
		$runtimePath = IWeb::$app->getBasePath().'runtime';
		$result      = IFile::clearDir($runtimePath);
		die("升级成功!! V5.3版本");
	}

	public function _c($sql)
	{
		return str_replace('{pre}',IWeb::$app->config['DB']['tablePre'],$sql);
	}
}