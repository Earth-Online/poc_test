-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 05 月 26 日 12:39
-- 服务器版本: 5.0.51
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `freephpyun31`
--

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_ad`
--

CREATE TABLE `phpyun_ad` (
  `id` int(20) NOT NULL auto_increment,
  `ad_name` varchar(100) NOT NULL,
  `did` int(11) NOT NULL default '0',
  `time_start` varchar(100) NOT NULL,
  `time_end` varchar(100) NOT NULL,
  `ad_type` varchar(10) NOT NULL,
  `word_info` text NOT NULL,
  `word_url` varchar(100) NOT NULL,
  `pic_url` varchar(100) NOT NULL,
  `pic_src` varchar(100) NOT NULL,
  `pic_width` varchar(100) NOT NULL,
  `pic_height` varchar(100) NOT NULL,
  `flash_url` varchar(100) default NULL,
  `flash_src` varchar(100) default NULL,
  `flash_width` varchar(100) default NULL,
  `flash_height` varchar(100) default NULL,
  `class_id` int(20) default NULL,
  `is_check` int(2) default '0',
  `target` int(2) default NULL,
  `is_open` int(2) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_announcement`
--

CREATE TABLE `phpyun_admin_announcement` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `datetime` int(11) NOT NULL,
  `did` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_config`
--

CREATE TABLE `phpyun_admin_config` (
  `name` varchar(255) NOT NULL,
  `config` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_link`
--

CREATE TABLE `phpyun_admin_link` (
  `id` int(8) NOT NULL auto_increment,
  `link_name` varchar(50) NOT NULL,
  `link_url` varchar(50) NOT NULL,
  `img_type` int(30) NOT NULL,
  `pic` varchar(50) NOT NULL,
  `link_type` varchar(1) NOT NULL,
  `link_state` int(1) NOT NULL default '0',
  `link_sorting` int(8) NOT NULL default '0',
  `link_time` varchar(20) NOT NULL,
  `domain` int(11) NOT NULL,
  `tem_type` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=116 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_log`
--

CREATE TABLE `phpyun_admin_log` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `username` varchar(200) default NULL,
  `content` text NOT NULL,
  `ctime` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_navigation`
--

CREATE TABLE `phpyun_admin_navigation` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `keyid` int(11) default '0',
  `url` varchar(50) character set gb2312 collate gb2312_bin default NULL,
  `menu` int(1) default NULL,
  `classname` varchar(100) default '0',
  `sort` int(5) default '0',
  `display` int(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1247 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_template`
--

CREATE TABLE `phpyun_admin_template` (
  `id` int(20) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `tp_name` varchar(50) NOT NULL,
  `update_time` int(32) NOT NULL,
  `dir` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=71 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_user`
--

CREATE TABLE `phpyun_admin_user` (
  `uid` int(3) NOT NULL auto_increment,
  `m_id` int(2) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lasttime` int(11) default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_admin_user_group`
--

CREATE TABLE `phpyun_admin_user_group` (
  `id` int(3) NOT NULL auto_increment,
  `group_name` varchar(100) NOT NULL,
  `group_power` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_ad_class`
--

CREATE TABLE `phpyun_ad_class` (
  `id` int(20) NOT NULL auto_increment,
  `class_name` varchar(100) NOT NULL,
  `orders` int(20) NOT NULL,
  `href` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_answer`
--

CREATE TABLE `phpyun_answer` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `comment` int(11) NOT NULL default '0',
  `support` int(11) NOT NULL default '0',
  `oppose` int(11) NOT NULL default '0',
  `content` text NOT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_answer_review`
--

CREATE TABLE `phpyun_answer_review` (
  `id` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL,
  `qid` int(11) default NULL,
  `uid` int(11) NOT NULL,
  `content` text NOT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_atn`
--

CREATE TABLE `phpyun_atn` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `sc_uid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `usertype` int(11) default NULL,
  `sc_usertype` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_attention`
--

CREATE TABLE `phpyun_attention` (
  `id` int(11) NOT NULL auto_increment,
  `ids` text NOT NULL,
  `type` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_bank`
--

CREATE TABLE `phpyun_bank` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `bank_name` varchar(200) default NULL,
  `bank_number` varchar(200) default NULL,
  `bank_address` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_banner`
--

CREATE TABLE `phpyun_banner` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `pic` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_blacklist`
--

CREATE TABLE `phpyun_blacklist` (
  `id` int(11) NOT NULL auto_increment,
  `p_uid` int(11) default NULL,
  `c_uid` int(11) default NULL,
  `inputtime` int(11) default NULL,
  `usertype` int(1) default NULL,
  `com_name` varchar(100) character set gb2312 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_city_class`
--

CREATE TABLE `phpyun_city_class` (
  `id` int(11) NOT NULL auto_increment,
  `keyid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `letter` varchar(1) NOT NULL,
  `display` int(1) NOT NULL,
  `sitetype` int(2) NOT NULL,
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1391 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_comclass`
--

CREATE TABLE `phpyun_comclass` (
  `id` int(11) NOT NULL auto_increment,
  `keyid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `variable` varchar(50) default NULL,
  `sort` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=113 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company`
--

CREATE TABLE `phpyun_company` (
  `uid` int(11) NOT NULL,
  `name` varchar(25) default NULL,
  `cert` varchar(100) default '0',
  `hy` int(5) default NULL,
  `pr` int(5) default NULL,
  `provinceid` int(5) default NULL,
  `cityid` int(5) default NULL,
  `mun` int(3) default NULL,
  `sdate` varchar(20) default NULL,
  `money` int(11) default NULL,
  `content` text,
  `address` varchar(100) default NULL,
  `zip` varchar(10) default NULL,
  `linkman` varchar(50) default NULL,
  `linkjob` varchar(50) default NULL,
  `linkqq` varchar(20) default NULL,
  `linkphone` varchar(20) default NULL,
  `linktel` varchar(50) default NULL,
  `linkmail` varchar(150) default NULL,
  `website` varchar(100) default NULL,
  `x` varchar(100) default NULL,
  `y` varchar(100) default NULL,
  `logo` varchar(100) default NULL,
  `payd` int(8) default '0',
  `integral` int(10) default '0',
  `lastupdate` varchar(10) default NULL,
  `cloudtype` int(2) default NULL,
  `jobtime` int(11) default NULL,
  `r_status` int(2) default '0',
  `firmpic` varchar(100) default NULL,
  `rec` int(11) default '0',
  `hits` int(11) default '0',
  `ant_num` int(11) default '0',
  `pl_time` int(11) default NULL,
  `pl_status` int(11) default '1',
  `order` int(11) unsigned default '0',
  `admin_remark` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_cert`
--

CREATE TABLE `phpyun_company_cert` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `type` varchar(200) default NULL,
  `status` int(11) default '0',
  `step` int(11) default NULL,
  `check` varchar(200) default NULL,
  `check2` varchar(200) default NULL,
  `ctime` int(11) default NULL,
  `statusbody` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_job`
--

CREATE TABLE `phpyun_company_job` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hy` int(5) default NULL,
  `job1` int(5) default NULL,
  `job1_son` int(5) default NULL,
  `job_post` int(5) default NULL,
  `provinceid` int(5) default NULL,
  `cityid` int(5) default NULL,
  `three_cityid` int(5) default NULL,
  `salary` int(5) default NULL,
  `type` int(5) NOT NULL,
  `number` int(2) NOT NULL,
  `exp` int(5) NOT NULL,
  `report` int(5) NOT NULL,
  `sex` int(5) NOT NULL,
  `edu` int(5) NOT NULL,
  `marriage` int(5) NOT NULL,
  `description` text NOT NULL,
  `sdate` int(11) NOT NULL,
  `edate` int(11) NOT NULL,
  `jobhits` int(10) NOT NULL default '0',
  `lastupdate` varchar(10) NOT NULL,
  `rec` int(2) default '0',
  `state` int(2) default '0',
  `statusbody` varchar(200) default '0',
  `age` int(11) default NULL,
  `lang` text,
  `welfare` text,
  `com_name` varchar(50) NOT NULL default '',
  `pr` int(5) default NULL,
  `mun` int(5) default NULL,
  `com_provinceid` int(5) default NULL,
  `rating` int(5) default NULL,
  `status` int(1) default '0',
  `urgent` int(1) default NULL,
  `r_status` int(1) default '0',
  `end_email` int(1) default '0',
  `rec_time` int(11) default NULL,
  `urgent_time` int(11) default NULL,
  `com_logo` varchar(100) default NULL,
  `autotype` int(11) default '0',
  `autotime` int(11) default '0',
  `is_link` int(11) default '1',
  `link_type` int(11) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_job_link`
--

CREATE TABLE `phpyun_company_job_link` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `jobid` int(11) default NULL,
  `link_man` varchar(100) default NULL,
  `link_moblie` varchar(20) default NULL,
  `email_type` int(5) default NULL,
  `is_email` int(2) default '0',
  `email` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_msg`
--

CREATE TABLE `phpyun_company_msg` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `cuid` int(11) default NULL,
  `content` text,
  `ctime` varchar(100) default NULL,
  `status` int(2) default NULL,
  `reply` text,
  `reply_time` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_news`
--

CREATE TABLE `phpyun_company_news` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0',
  `title` varchar(200) default '0',
  `ctime` int(11) default '0',
  `body` text,
  `status` int(2) default '0',
  `statusbody` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_order`
--

CREATE TABLE `phpyun_company_order` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `order_id` varchar(18) default NULL,
  `order_type` varchar(25) default NULL,
  `order_price` double(18,2) NOT NULL,
  `order_time` int(10) NOT NULL,
  `order_state` int(2) NOT NULL,
  `order_remark` text,
  `order_bank` varchar(150) NOT NULL default '0',
  `type` int(1) default NULL,
  `rating` int(10) default NULL,
  `integral` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_pay`
--

CREATE TABLE `phpyun_company_pay` (
  `id` int(11) NOT NULL auto_increment,
  `order_id` varchar(18) default NULL,
  `order_price` decimal(10,2) default NULL,
  `pay_time` int(11) default NULL,
  `pay_state` int(2) default NULL,
  `com_id` int(10) default NULL,
  `pay_remark` varchar(255) default NULL,
  `type` int(1) default NULL,
  `pay_type` int(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_product`
--

CREATE TABLE `phpyun_company_product` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0',
  `title` varchar(200) default '0',
  `pic` varchar(200) default '0',
  `body` text,
  `ctime` int(11) default '0',
  `status` int(2) default '0',
  `statusbody` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_rating`
--

CREATE TABLE `phpyun_company_rating` (
  `id` int(6) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `service_price` varchar(100) default NULL,
  `integral_buy` varchar(100) default NULL,
  `resume` int(5) default NULL,
  `job_num` int(11) default NULL,
  `interview` int(11) default NULL,
  `editjob_num` int(11) default NULL,
  `breakjob_num` int(11) default NULL,
  `talent_num` int(10) default NULL,
  `sort` int(10) default NULL,
  `display` int(1) default NULL,
  `explains` varchar(255) default NULL,
  `com_pic` varchar(100) default NULL,
  `com_color` varchar(100) default NULL,
  `type` int(2) default NULL,
  `category` int(2) default NULL,
  `service_time` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_show`
--

CREATE TABLE `phpyun_company_show` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200)  default NULL,
  `picurl` varchar(200)  default NULL,
  `body` varchar(200) default NULL,
  `ctime` varchar(200)  default NULL,
  `uid` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_statis`
--

CREATE TABLE `phpyun_company_statis` (
  `uid` int(11) NOT NULL,
  `pay` double(10,2) NOT NULL default '0.00',
  `integral` varchar(10) NOT NULL default '0',
  `job` int(10) unsigned default '0',
  `status0` int(5) unsigned NOT NULL default '0',
  `status1` int(5) unsigned NOT NULL default '0',
  `status2` int(5) unsigned NOT NULL default '0',
  `status3` int(5) NOT NULL default '0',
  `sq_job` int(6) unsigned NOT NULL,
  `fav_job` int(6) unsigned NOT NULL,
  `rating` int(5) unsigned default NULL,
  `rating_name` varchar(100) default NULL,
  `vip_etime` varchar(100) default '0',
  `all_pay` double(10,2) NOT NULL,
  `consum_pay` double(10,2) NOT NULL,
  `talent_num` int(11) default NULL,
  `invite_resume` int(10) default NULL,
  `comtpl` varchar(100) default '0',
  `comtpl_all` varchar(100) default NULL,
  `job_num` int(11) default '0',
  `editjob_num` int(11) default '0',
  `breakjob_num` int(11) default '0',
  `down_resume` int(10) default '0',
  `autotime` int(11) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_company_tpl`
--

CREATE TABLE `phpyun_company_tpl` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100)  default '0',
  `url` varchar(100)  default '0',
  `pic` varchar(200)  default '0',
  `type` int(10) default '0',
  `price` varchar(100)  default '0',
  `status` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_cron`
--

CREATE TABLE `phpyun_cron` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `dir` varchar(200) default NULL,
  `type` int(11) default NULL,
  `week` int(11) default NULL,
  `month` int(10) default NULL,
  `hour` int(10) default NULL,
  `minute` int(10) default NULL,
  `display` int(1) default NULL,
  `ctime` int(11) default NULL,
  `nowtime` int(11) default '0',
  `nexttime` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_description`
--

CREATE TABLE `phpyun_description` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `keyword` varchar(255) default NULL,
  `descs` text,
  `top_tpl` int(2) default NULL,
  `top_tpl_dir` varchar(255) default NULL,
  `footer_tpl` int(2) default NULL,
  `footer_tpl_dir` varchar(255) default NULL,
  `content` text,
  `sort` int(11) default NULL,
  `is_nav` int(1) default NULL,
  `ctime` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_domain`
--

CREATE TABLE `phpyun_domain` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL,
  `domain` varchar(200) NOT NULL,
  `province` int(11) default NULL,
  `cityid` int(11) default NULL,
  `three_cityid` int(11) default NULL,
  `type` int(2) NOT NULL,
  `style` varchar(100)  NOT NULL,
  `hy` int(11) default NULL,
  `fz_type` int(11) NOT NULL,
  `webtitle` text,
  `webkeyword` text,
  `webmeta` text,
  `weblogo` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_down_resume`
--

CREATE TABLE `phpyun_down_resume` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `eid` int(11) default NULL,
  `comid` int(11) default NULL,
  `downtime` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_fav_job`
--

CREATE TABLE `phpyun_fav_job` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `com_id` int(11) NOT NULL,
  `com_name` varchar(150) NOT NULL,
  `datetime` int(10) NOT NULL,
  `type` int(11) NOT NULL default '1',
  `job_name` varchar(150) default NULL,
  `job_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend`
--

CREATE TABLE `phpyun_friend` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `nid` int(11) default NULL,
  `status` int(11) default NULL,
  `uidtype` int(2) default NULL,
  `nidtype` int(2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_foot`
--

CREATE TABLE `phpyun_friend_foot` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `fid` int(11) default NULL,
  `num` int(11) default NULL,
  `ctime` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_info`
--

CREATE TABLE `phpyun_friend_info` (
  `uid` int(11) default NULL,
  `nickname` varchar(100)  default NULL,
  `sex` int(1) default '3',
  `pic` varchar(100)  default NULL,
  `pic_big` varchar(100) default NULL,
  `description` varchar(100)  default NULL,
  `birthday` varchar(100)  default NULL,
  `usertype` int(2) default NULL,
  `iscert` int(2) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_message`
--

CREATE TABLE `phpyun_friend_message` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL,
  `uid` int(11) default NULL,
  `u_name` varchar(100)  default NULL,
  `fid` int(11) default NULL,
  `f_name` varchar(100)  default NULL,
  `nid` int(11) default '0',
  `content` varchar(225)  default NULL,
  `ctime` int(11) default NULL,
  `status` int(11) default '0',
  `remind_status` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_reply`
--

CREATE TABLE `phpyun_friend_reply` (
  `id` int(11) NOT NULL auto_increment,
  `nid` int(11) default NULL,
  `fid` int(11) default NULL,
  `uid` int(11) default NULL,
  `reply` varchar(225)  default NULL,
  `ctime` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_friend_state`
--

CREATE TABLE `phpyun_friend_state` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `content` varchar(225)  default NULL,
  `ctime` int(11) default NULL,
  `type` int(11) default '1',
  `msg_pic` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_hotjob`
--

CREATE TABLE `phpyun_hotjob` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `username` varchar(200) default NULL,
  `rating` varchar(20) default NULL,
  `hot_pic` varchar(100) default NULL,
  `service_price` int(11) default NULL,
  `time_start` int(11) default NULL,
  `time_end` int(11) default NULL,
  `sort` int(11) default '0',
  `beizhu` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_hot_key`
--

CREATE TABLE `phpyun_hot_key` (
  `id` int(20) NOT NULL auto_increment,
  `key_name` varchar(100) NOT NULL,
  `num` int(20) NOT NULL default '0',
  `type` int(2) NOT NULL,
  `size` varchar(10) default NULL,
  `check` int(1) default '0',
  `color` varchar(10) default NULL,
  `bold` int(11) default NULL,
  `tuijian` int(11) default NULL,
  `wxtime` int(11) default '0',
  `wxuser` varchar(100) default NULL,
  `wxid` varchar(100) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_industry`
--

CREATE TABLE `phpyun_industry` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `sort` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=840 ;



-- --------------------------------------------------------

--
-- 表的结构 `phpyun_job_class`
--

CREATE TABLE `phpyun_job_class` (
  `id` int(11) NOT NULL auto_increment,
  `keyid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=953 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_look_resume`
--

CREATE TABLE `phpyun_look_resume` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `com_id` int(11) default NULL,
  `resume_id` int(11) default NULL,
  `datetime` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_member`
--

CREATE TABLE `phpyun_member` (
  `uid` int(11) NOT NULL auto_increment,
  `username` varchar(100) default NULL,
  `password` varchar(32) default NULL,
  `email` varchar(200) default NULL,
  `moblie` varchar(15) default NULL,
  `reg_ip` varchar(20) default NULL,
  `reg_date` int(11) default NULL,
  `login_ip` varchar(20) default NULL,
  `login_date` int(11) default NULL,
  `usertype` int(1) NOT NULL default '1',
  `login_hits` int(11) default '0',
  `salt` varchar(6) default NULL,
  `address` varchar(100) default NULL,
  `name_repeat` int(2) default '0',
  `qqid` varchar(200) default NULL,
  `status` int(4) default NULL,
  `pwuid` int(11) default '0',
  `pw_repeat` int(1) default '0',
  `lock_info` varchar(200) character set gb2312 default NULL,
  `email_status` int(1) default NULL,
  `signature` varchar(100) default NULL,
  `sinaid` varchar(100) default NULL,
  `wxid` varchar(100) default '0',
  `wxname` varchar(100) default NULL,
  `wxbindtime` int(11) default '0',
  `passtext` varchar(100) default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_member_statis`
--

CREATE TABLE `phpyun_member_statis` (
  `uid` int(11) NOT NULL,
  `integral` varchar(10) NOT NULL default '0',
  `pay` double(10,2) NOT NULL default '0.00',
  `resume_num` int(10) NOT NULL,
  `fav_jobnum` int(10) NOT NULL,
  `sq_jobnum` int(10) NOT NULL,
  `message_num` int(10) NOT NULL,
  `down_num` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_message`
--

CREATE TABLE `phpyun_message` (
  `id` int(11) NOT NULL auto_increment,
  `content` varchar(255) NOT NULL,
  `username` varchar(20) default NULL,
  `uid` int(11) default NULL,
  `status` int(1) default '0',
  `ctime` int(11) default NULL,
  `keyid` int(11) default '0',
  `fa_uid` int(11) default '0',
  `remind_status` int(11) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_moblie_msg`
--

CREATE TABLE `phpyun_moblie_msg` (
  `id` int(11) NOT NULL auto_increment,
  `moblie` varchar(200) default NULL,
  `content` varchar(200) default NULL,
  `ctime` int(11) default NULL,
  `state` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_msg`
--

CREATE TABLE `phpyun_msg` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default NULL,
  `username` varchar(100) default NULL,
  `jobid` int(11) default NULL,
  `job_uid` int(11) default NULL,
  `datetime` int(11) default NULL,
  `reply` text,
  `content` text,
  `reply_time` int(11) default NULL,
  `com_name` varchar(100) default NULL,
  `job_name` varchar(100) default NULL,
  `del_status` int(11) default '0',
  `type` int(11) default '1',
  `user_remind_status` int(11) default '1',
  `com_remind_status` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_navigation`
--

CREATE TABLE `phpyun_navigation` (
  `id` int(11) NOT NULL auto_increment,
  `nid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) default NULL,
  `sort` int(11) default NULL,
  `display` int(1) NOT NULL,
  `eject` int(1) NOT NULL,
  `type` int(1) default '1',
  `furl` varchar(100) default NULL,
  `color` varchar(20) default NULL,
  `model` varchar(20) default NULL,
  `bold` int(11) default 0,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_navigation_type`
--

CREATE TABLE `phpyun_navigation_type` (
  `id` int(11) NOT NULL auto_increment,
  `typename` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_news_base`
--

CREATE TABLE `phpyun_news_base` (
  `id` int(11) NOT NULL auto_increment,
  `nid` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `color` varchar(255) default NULL,
  `keyword` varchar(200) NOT NULL,
  `author` varchar(200) NOT NULL,
  `datetime` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `describe` varchar(11) NOT NULL,
  `description` varchar(255) default NULL,
  `newsphoto` varchar(100) default NULL,
  `s_thumb` varchar(100) default NULL,
  `source` varchar(255) default NULL,
  `sort` int(11) default NULL,
  `lastupdate` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_news_content`
--

CREATE TABLE `phpyun_news_content` (
  `nbid` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`nbid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_news_group`
--

CREATE TABLE `phpyun_news_group` (
  `id` int(11) NOT NULL auto_increment,
  `keyid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort` int(11) default '0',
  `rec`  int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_once_job`
--

CREATE TABLE `phpyun_once_job` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL,
  `mans` varchar(100) NOT NULL,
  `require` varchar(255) NOT NULL,
  `companyname` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `linkman` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `ctime` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `password` varchar(100) NOT NULL,
  `qq` varchar(20) default NULL,
  `email` varchar(150) default NULL,
  `edate` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_outside`
--

CREATE TABLE `phpyun_outside` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `type` varchar(100) default NULL,
  `titlelen` int(10) default NULL,
  `infolen` int(10) default NULL,
  `byorder` varchar(200) default NULL,
  `num` int(11) default NULL,
  `code` text,
  `edittime` int(10) default NULL,
  `lasttime` int(11) default NULL,
  `urltype` varchar(200) default NULL,
  `timetype` varchar(200) default NULL,
  `where` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_property`
--

CREATE TABLE `phpyun_property` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) default NULL,
  `value` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_question`
--

CREATE TABLE `phpyun_question` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `answer_num` int(11) NOT NULL default '0',
  `visit` int(11) NOT NULL default '0',
  `is_recom` int(1) NOT NULL default '0',
  `last_time` int(11) default NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_q_class`
--

CREATE TABLE `phpyun_q_class` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `pid` int(11) NOT NULL,
  `pic` varchar(100) default NULL,
  `sort` int(11) NOT NULL,
  `intro` text,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_reason`
--

CREATE TABLE `phpyun_reason` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_report`
--

CREATE TABLE `phpyun_report` (
  `id` int(11) NOT NULL auto_increment,
  `p_uid` int(11) default NULL,
  `c_uid` int(11) default NULL,
  `eid` int(11) default NULL,
  `usertype` int(1) default NULL,
  `inputtime` int(11) default NULL,
  `username` varchar(100) character set gb2312 default NULL,
  `r_name` varchar(100) character set gb2312 default NULL,
  `status` int(1) default '0',
  `r_reason` varchar(200) character set gb2312 default NULL,
  `type` int(11) default '0',
  `r_type` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume`
--

CREATE TABLE `phpyun_resume` (
  `uid` int(11) NOT NULL,
  `name` varchar(25) default NULL,
  `sex` int(2) default NULL,
  `birthday` varchar(10) default NULL,
  `marriage` varchar(2) default NULL,
  `height` varchar(4) default NULL,
  `nationality` varchar(20) default NULL,
  `weight` varchar(4) default NULL,
  `idcard` varchar(20) default NULL,
  `telphone` varchar(20) default NULL,
  `telhome` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `edu` int(2) default NULL,
  `homepage` varchar(50) default NULL,
  `address` varchar(80) default NULL,
  `description` varchar(150) default NULL,
  `resume_photo` varchar(100) default NULL,
  `photo` varchar(100) default NULL,
  `expect` int(2) default '0',
  `def_job` int(11) default '0',
  `exp` int(11) default NULL,
  `status` int(2) default '1',
  `idcard_pic` varchar(100) default NULL,
  `idcard_status` int(1) default NULL,
  `statusbody` varchar(200) default NULL,
  `cert_time` int(11) default NULL,
  `r_status` int(1) default '0',
  `ant_num` int(11) default '0',
  `living` varchar(100) default NULL,
  `domicile` varchar(100) default NULL,
  `basic_info` int(11) default '1',
  KEY `def_job` (`def_job`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_cert`
--

CREATE TABLE `phpyun_resume_cert` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) default NULL,
  `edate` int(10) default NULL,
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_doc`
--

CREATE TABLE `phpyun_resume_doc` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `doc` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_edu`
--

CREATE TABLE `phpyun_resume_edu` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) default NULL,
  `edate` int(10) default NULL,
  `specialty` varchar(50) default NULL,
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_expect`
--

CREATE TABLE `phpyun_resume_expect` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `name` varchar(25) default NULL,
  `hy` int(5) default NULL,
  `job_classid` varchar(100) default NULL,
  `provinceid` int(5) default '0',
  `cityid` int(5) default '0',
  `three_cityid` int(5) default '0',
  `salary` int(3) default '0',
  `type` int(3) default '0',
  `report` int(3) default '0',
  `defaults` int(1) NOT NULL default '0',
  `open` int(1) default '1',
  `full` int(3) default '0',
  `doc` int(1) default '0',
  `hits` int(6) default '0',
  `lastupdate` int(10) NOT NULL default '0',
  `def_job` int(11) default '0',
  `olduid` int(11) default '0',
  `integrity` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_other`
--

CREATE TABLE `phpyun_resume_other` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_project`
--

CREATE TABLE `phpyun_resume_project` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) default NULL,
  `edate` int(10) default NULL,
  `sys` varchar(50) default NULL,
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_skill`
--

CREATE TABLE `phpyun_resume_skill` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `skill` int(5) NOT NULL,
  `ing` int(5) NOT NULL,
  `longtime` int(5) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_tiny`
--

CREATE TABLE `phpyun_resume_tiny` (
  `id` int(25) NOT NULL auto_increment,
  `username` varchar(25) NOT NULL,
  `password` varchar(50) NOT NULL,
  `sex` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  `job` varchar(25) NOT NULL,
  `mobile` varchar(25) NOT NULL,
  `qq` varchar(25) NOT NULL,
  `production` text NOT NULL,
  `time` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_training`
--

CREATE TABLE `phpyun_resume_training` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) default NULL,
  `edate` int(10) default NULL,
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_resume_work`
--

CREATE TABLE `phpyun_resume_work` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sdate` int(10) default NULL,
  `edate` int(10) default NULL,
  `department` varchar(50) default NULL,
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_seo`
--

CREATE TABLE `phpyun_seo` (
  `id` int(11) NOT NULL auto_increment,
  `seoname` varchar(100) default NULL,
  `ident` varchar(100) default NULL,
  `title` varchar(100) default NULL,
  `keywords` varchar(255) default NULL,
  `description` text,
  `time` int(11) default NULL,
  `affiliation` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_sysmsg`
--

CREATE TABLE `phpyun_sysmsg` (
  `id` int(11) NOT NULL auto_increment,
  `content` varchar(255) NOT NULL,
  `fa_uid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_templates`
--

CREATE TABLE `phpyun_templates` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `title` varchar(255) default NULL,
  `content` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_userclass`
--

CREATE TABLE `phpyun_userclass` (
  `id` int(11) NOT NULL auto_increment,
  `keyid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `variable` varchar(100) default NULL,
  `sort` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_userid_job`
--

CREATE TABLE `phpyun_userid_job` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `job_name` varchar(150) NOT NULL,
  `com_id` int(11) NOT NULL,
  `com_name` varchar(150) NOT NULL,
  `eid` int(10) NOT NULL,
  `datetime` int(10) NOT NULL,
  `type` int(1) NOT NULL default '1',
  `is_browse` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_userid_msg`
--

CREATE TABLE `phpyun_userid_msg` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `fid` int(11) NOT NULL,
  `fname` varchar(150) NOT NULL,
  `type` int(11) NOT NULL default '0',
  `datetime` int(10) NOT NULL,
  `default` int(1) default '0',
  `is_browse` int(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_user_resume`
--

CREATE TABLE `phpyun_user_resume` (
  `id` int(10) NOT NULL auto_increment,
  `uid` int(10) NOT NULL,
  `eid` int(10) NOT NULL,
  `expect` int(1) default '0',
  `skill` int(1) default '0',
  `work` int(1) default '0',
  `project` int(1) default '0',
  `edu` int(1) default '0',
  `training` int(1) default '0',
  `cert` int(1) default '0',
  `other` int(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_webscan360`
--

CREATE TABLE `phpyun_webscan360` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `var` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `ext1` varchar(100) default NULL,
  `ext2` varchar(100) default NULL,
  `state` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_wxlog`
--

CREATE TABLE `phpyun_wxlog` (
  `id` int(11) NOT NULL auto_increment,
  `wxid` varchar(100)  NOT NULL default '0',
  `wxname` varchar(100) default NULL,
  `wxuid` int(11) default '0',
  `wxuser` varchar(100) default NULL,
  `content` text,
  `reply` text,
  `nav` varchar(100) default NULL,
  `type` varchar(100) default NULL,
  `time` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_wxnav`
--

CREATE TABLE `phpyun_wxnav` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `keyid` int(11) default NULL,
  `key` varchar(100) default NULL,
  `url` varchar(100) default NULL,
  `type` varchar(50) NOT NULL,
  `sort` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_zhaopinhui`
--

CREATE TABLE `phpyun_zhaopinhui` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) default '0',
  `pic` varchar(200) default '0',
  `starttime` varchar(100) default '0',
  `endtime` varchar(100) default '0',
  `provinceid` int(11) default '0',
  `cityid` int(11) default '0',
  `address` varchar(200) default NULL,
  `traffic` text,
  `phone` varchar(100) default '0',
  `organizers` varchar(200) default '0',
  `user` varchar(200) default NULL,
  `weburl` varchar(100) default '0',
  `body` text,
  `media` text,
  `packages` text,
  `booth` text,
  `participate` text,
  `status` int(11) default '0',
  `ctime` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_zhaopinhui_com`
--

CREATE TABLE `phpyun_zhaopinhui_com` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0',
  `zid` int(11) default '0',
  `jobid` varchar(255) default '0',
  `ctime` int(11) default '0',
  `status` int(11) default '0',
  `statusbody` varchar(100) default NULL,
  `inadd` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `phpyun_zhaopinhui_pic`
--

CREATE TABLE `phpyun_zhaopinhui_pic` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) default '0',
  `pic` varchar(200) default '0',
  `sort` int(11) default '0',
  `zid` int(11) default '0',
  `is_themb` int(5) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;
