<?php
require '../inc/zzz_class.php';
$act = getModule();
switch ( $act ) {
    case 'reg'      : reg();break;
    case 'login'    : login(); break;
    case 'forget'   : forget(); break;
    case 'dels'     : dels();  break;
    case 'editface' : editface(); break;
    case 'edituser' :  edituser(); break;
    case 'editpwd'  : editpwd(); break;
    case 'loginout' : loginout();  break;
    case 'gbook'    : gbook(); break;
    case 'getjson'  :  get_json(); break;
}

function gbook() {
    $conf = _SERVER( 'conf' );
    $type = getForm( 'type', 'post' );
    $userid = getform( "userid", "post", '', 0 );
    $code = getform( "code", "post", 'gbookcode' );
    $backurl = _POST( "backurl" );
    $ip = ip();
    $mailbody = '';
    $today = date( 'Y-m-d' );
    if ( $conf[ 'gbookuser' ] == 1 && isnul( get_session( 'uid' ) ) )back( '很抱歉！要求会员级别以上用户，请先登陆！' );

    $maxnum = isnul( get_session( 'uid' ) ) ? $conf[ 'gbookanonymousnum' ] : $conf[ 'gbookusernum' ];
    if ( $conf[ 'db' ][ 'type' ] == 'access' ) {
        $nownum = db_count( 'gbook', "g_time>date() and g_ip='" . $ip . "'" );
    } elseif ( $conf[ 'db' ][ 'type' ] == 'sqlite' ) {
        $nownum = db_count( 'gbook', "g_time>'" . $today . "' and g_ip='" . $ip . "'" );
    } elseif ( $conf[ 'db' ][ 'type' ] == 'mysql' ) {
        $nownum = db_count( 'gbook', "date(g_time)='" . $today . "' and g_ip='" . $ip . "'" );
    }
    $nownum >= $maxnum and back( '每天最多留言' . $maxnum . '次,您留言' . $nownum . '次，请明日再来' );
    $colarr = array( 'lid' => 1, 'g_uid' => $userid, 'g_order' => 9, 'g_onoff' => conf( 'gbookonoff' ), 'g_time' => date( 'Y-m-d H:i:s' ), 'g_ip' => $ip );
    if ( $conf[ 'gbookname_onoff' ] == 1 ) {
        $gname = safe_key( getform( "gname", "post", $conf[ 'gbookname_test' ] ) );
        arr_add( $colarr, 'g_name', $gname );
        $mailbody .= $conf[ 'gbookname' ] . ':' . $gname . '<br>';
    }
    if ( $conf[ 'gbooktitle_onoff' ] == 1 ) {
        $gtitle = safe_key( getform( "gtitle", "post", $conf[ 'gbooktitle_test' ] ) );
        arr_add( $colarr, 'g_title', $gtitle );
        $mailbody .= $conf[ 'gbooktitle' ] . ':' . $gtitle . '<br>';
    }
    if ( $conf[ 'gbooktel_onoff' ] == 1 ) {
        $gtel = safe_key( getform( "gtel", "post", $conf[ 'gbooktel_test' ] ) );
        arr_add( $colarr, 'g_tel', $gtel );
        $mailbody .= $conf[ 'gbooktel' ] . ':' . $gtel . '<br>';
    }
    if ( $conf[ 'gbookmail_onoff' ] == 1 ) {
        $gmail = safe_key( getform( "gmail", "post", $conf[ 'gbookmail_test' ] ) );
        arr_add( $colarr, 'g_email', $gmail );
        $mailbody .= $conf[ 'gbookmail' ] . ':' . $gmail . '<br>';
    }
    if ( $conf[ 'gbookcontent_onoff' ] == 1 ) {
        $gcontent = safe_key( getform( "gcontent", "post", $conf[ 'gbookcontent_test' ] ) );
        arr_add( $colarr, 'g_content', $gcontent );
        $mailbody .= $conf[ 'gbookcontent' ] . ':' . $gcontent . '<br>';
    }
    $backurl = empty( $backurl ) ? SITE_PATH : str_replace( 'ＰＨＰ', 'php', $backurl );
    $data = db_load( 'content_custom', "customType = 'gbook'", 'customname,custom' );
    foreach ( $data as $value ) {
        $val = safe_key( getform( $value[ 'custom' ], "post" ) );
        arr_add( $colarr, $value[ 'custom' ], $val );
        $mailbody .= $value[ 'customname' ] . ':' . $val . '<br>';
    }
    db_insert( 'gbook', $colarr );
    if ( $conf[ 'gbooksendmail' ] == 1 )alertmail( '新留言提醒！', $mailbody );
    echo '<script>alert("留言成功");parent.location.href="' . $backurl . '";</script>';
}

function reg() {
    $type = getForm( 'type', 'post' );
    $backurl = getForm( 'backurl', 'post' );
    $check = array( 'lenmin' => 4, 'lenmax' => 16 );
    $username = safe_key( getform( 'username', 'post', $check ) );
    if ( check_used( "username='" . $username . "'" ) )back( '账户已被使用，请更换' );
    $password = getform( 'password', 'post', $check );
    $mobile = safe_key( getform( 'mobile', 'post' ) );
    if ( check_used( "mobile='" . $mobile . "'" ) )back( '手机号已被使用，请更换' );
    $email = getform( 'email', 'post' );
    $code = getform( 'code', 'post', 'usercode' );
    $regtime = date( 'Y-m-d H:i:s' );
    if ( conf( 'smsmark' ) == 1 && conf( 'regsendsms' ) == 1 ) {
        $smscode = getform( 'smscode', 'post' );
        if ( $mobile != get_session( "smsphone" ) || $smscode != get_session( "smscode" ) )back( '手机验证码不正确，请重试' );
    }
    $colarr = array( 'username' => $username, 'password' => md5_16( $password ), 'mobile' => $mobile, 'email' => $email, 'regtime' => $regtime, 'u_onoff' => conf( 'useronoff' ), 'u_gid' => 4, 'u_order' => 9, 'balance' => 0, 'points' => 0, 'logincount' => 0 );
    $backurl = empty( $backurl ) ? SITE_PATH . '?user/': str_replace( 'ＰＨＰ', 'php', $backurl );
    if ( db_insert( 'user', $colarr ) ) {
        if ( conf( 'regsendmail' ) == 1 ) {
            $mailbody = '会员：' . $username . '手机' . $mobile;
            alertmail( '会员注册提醒！', $mailbody );
        }
        if ( conf( 'useronoff' ) == 1 ) {
            loginin( $username );
            $type == 'open' ? layertrue( '注册成功' ) : ok( '注册成功,欢迎您' . $username . '！', $backurl );
        } else {
            $type == 'open' ? layertrue( '注册成功，等待审核' ) : ok( '注册成功，需要审核后方可登陆', $backurl );
        }
    }
}

function login() {
    $type = getForm( 'type', 'post' );
    $backurl = getForm( 'backurl', 'post' );
    $check = array( 'lenmin' => 4, 'lenmax' => 16 );
    $username = safe_key( getForm( 'username', 'post', $check ) );
    $password = getForm( 'password', 'post', $check );
    $code = getForm( 'code', 'post', 'usercode' );
    $count = db_count( 'user', "u_onoff=1 and (username='" . $username . "' or mobile='" . $username . "') and password='" . md5_16( $password ) . "'" );
    $backurl = empty( $backurl ) ? SITE_PATH . '?user/': str_replace( 'ＰＨＰ', 'php', $backurl );
    if ( $count > 0 ) {
        loginin( $username );
        if ( conf( 'loginsendmail' ) == 1 ) {
            $mailbody = '会员：' . $username;
            alertmail( '会员登陆提醒！', $mailbody );
        }
        if ( $type == "open" ) {
            layertrue( '登录成功' );
        } else {
            phpgo( $backurl );
        }
    } else {
        back( '登录失败，请检查用户名、密码是否正确？是否还再审核中？' );
    }
}

function loginin( $username ) {
    $data = db_load_sql( "select uid,username,truename,tel,mobile,email,qq,face,gid,g_name,g_mark from [dbpre]user as a,[dbpre]user_group as b where a.u_gid=b.gid and (username ='" . $username . "' or mobile ='" . $username . "')" );
    foreach ( $data as $value ) {
        $GLOBALS[ 'username' ] = set_session( "username", $value[ 'username' ] );
        $GLOBALS[ 'gmark' ] = set_session( "gmark", $value[ 'g_mark' ] );
        $GLOBALS[ 'uid' ] = set_session( "uid", $value[ 'uid' ] );
        $GLOBALS[ 'gid' ] = set_session( "gid", $value[ 'gid' ] );
        $GLOBALS[ 'truename' ] = set_session( "truename", $value[ 'truename' ] );
        $GLOBALS[ 'useremail' ] = set_session( "useremail", $value[ 'email' ] );
        $GLOBALS[ 'usermobile' ] = set_session( "usermobile", $value[ 'mobile' ] );
        $GLOBALS[ 'usertel' ] = set_session( "usertel", $value[ 'tel' ] );
        $GLOBALS[ 'userqq' ] = set_session( "userqq", $value[ 'qq' ] );
        $face = empty( $value[ 'face' ] ) ? $value[ 'face' ] : "noface.png";
        $GLOBALS[ 'userface' ] = set_session( "userface", $face );
        $GLOBALS[ 'gname' ] = set_session( "gname", $value[ 'g_name' ] );
        arr_add( $val, 'lastlogintime', date( 'Y-m-d H:i:s' ) );
        arr_add( $val, 'lastloginip', ip() );
        arr_add( $val, 'logincount+', 1 );
        db_update( 'user', 'uid=' . $value[ 'uid' ], $val );
    }
}

function forget() {
    $type = safe_key( getForm( 'type', 'post' ) );
    $backurl = getForm( 'backurl', 'post' );
    $mobile = safe_key( getForm( 'mobile', 'post' ) );
    $question = safe_key( getForm( 'question', 'post' ) );
    $answer = safe_key( getForm( 'answer', 'post' ) );
    $count = db_count( 'user', "u_onoff=1 and (username='" . $mobile . "' or mobile='" . $mobile . "') and question='" . $question . "'  and answer='" . $answer . "'" );
    $backurl = empty( $backurl ) ? SITE_PATH . '?user/': str_replace( 'ＰＨＰ', 'php', $backurl );
    if ( $count > 0 ) {
        loginin( $mobile );
        if ( conf( 'forgetsendmail' ) == 1 ) {
            $mailbody = '会员：' . $mobile;
            alertmail( '会员找回密码提醒！', $mailbody );
        }
        ok( '验证成功，请进入会员中心修改密码', $backurl );
    } else {
        error( '验证失败,请确认问题答案是否正确', SITE_PATH . '?user/forget' );
    }
}

function editpwd() {
    $uid = getform( 'uid', 'post' );
    $uid != get_session( 'uid' )and back( '很抱歉，密码修改失败' );
    $check = array( 'lenmin' => 4, 'lenmax' => 16 );
    $password = getform( 'password', 'post', 'nul' );
    $newpassword = getform( 'newpassword', 'post', $check );
    $question = getform( 'question', 'post', $check );
    $answer = getform( 'answer', 'post', $check );
    $colarr = array( 'password' => md5_16( $newpassword ), 'question' => $question, 'answer' => $answer );
    if ( db_count( 'user', array( 'uid' => $uid, 'password' => md5_16( $password ) ) ) > 0 ) {
        db_update( 'user', array( 'uid' => $uid ), $colarr );
        ok( '修改成功,密码修改完成！', SITE_PATH . '?user/' );
    } else {
        back( '很抱歉，密码修改失败' );
    }
}

function loginout() {
    del_session( "username" );
    del_session( "uid" );
    del_session( "gid" );
    del_session( "gmark" );
    del_session( "truename" );
    del_session( "useremail" );
    del_session( "usermobile" );
    del_session( "usertel" );
    del_session( "userqq" );
    del_session( "userface" );
    del_session( "gname" );
    phpgo( SITE_PATH . "?user/login" );
}

function editface() {
    $uid = getform( 'uid', 'post' );
    $face = getform( 'face', 'post' );
    db_update( 'user', array( 'uid' => $uid ), array( 'face' => $face ) );
    echo 1;
}

function edituser() {
    $uid = getform( 'uid', 'post' );
    $uid != get_session( 'uid' )and back( '很抱歉，资料修改失败' );
    $truename = getform( 'truename', 'post' );
    $telcode = getform( 'telcode', 'post' );
    $tel = getform( 'tel', 'post' );
    $tel = empty($telcode) ?  $tel : $telcode . '-' . $tel;
    $mobile = getform( 'mobile', 'post' );
    $face = getform( 'face', 'post' );
    $password = getform( 'password', 'post' );
    $email = getform( 'email', 'post' );
    $qq = getform( 'qq', 'post' );
    $province = getform( 'province', 'post' );
    $city = getform( 'city', 'post' );
    $district = getform( 'district', 'post' );
    $address = getform( 'address', 'post' );
    $post = getform( 'post', 'post' );
    $desc = getform( 'desc', 'post' );
    $colarr = array( 'truename' => $truename, 'tel' => $tel, 'email' => $email, 'face' => $face, 'qq' => $qq, 'province' => $province, 'city' => $city, 'district' => $district, 'address' => $address, 'post' => $post, 'u_desc' => $desc );
    if ( !empty( $mobile ) ) {
        arr_add( $colarr, 'mobile', $mobile );
        // arr_add($colarr,'username',$mobile);
    }
    if ( !empty( $password ) ) {
        arr_add( $colarr, 'password', $password );
    }
    db_update( 'user', array( 'uid' => $uid ), $colarr );

    ok( '修改成功,资料修改完成！', SITE_PATH . '?user/' );
}

//支持任意查询输出json，只支持post提交
function get_json() {
    $table = getform( 'table', 'post' );
    $arr = array( 'user', 'user_group', 'log_err', 'log_dbsql', 'log_dbbackup', 'content_custom' );
    in_array( $table, $arr )and exit;
    $col = getform( 'col', 'post' );
    $where = getform( 'where', 'post' );
    if ( empty( $col ) || empty( $where ) )exit;
    $num = getform( 'num', 'post' );
    $num = empty( $num ) ? 100 : $num;
    $page = getform( 'page', 'post' );
    $page = empty( $page ) ? 1 : $page;
    $order = getform( 'order', 'post' );
    $order = isset( $order ) ? $order : '';
    $data = db_load( $table, $where, $col, $num, $order, $page );
    echo tojson( $data );
}