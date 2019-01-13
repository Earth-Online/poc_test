<?php

function cache_new( $cacheconf ) {
    // 缓存初始化，这里并不会产生连接！在真正使用的时候才连接。
    // 这里采用最笨拙的方式而不采用 new $classname 的方式，有利于 opcode 缓存。
    if ( $cacheconf && !empty( $cacheconf[ 'enable' ] ) ) {
        switch ( $cacheconf[ 'type' ] ) {
            case 'redis':
                $cache = new cache_redis( $cacheconf[ 'redis' ] );
                break;
            case 'memcached':
                $cache = new cache_memcached( $cacheconf[ 'memcached' ] );
                break;
            case 'pdo_mysql':
            case 'mysql':
                $cache = new cache_mysql( $cacheconf[ 'mysql' ] );
                break;
            case 'xcache':
                $cache = new cache_xcache( $cacheconf[ 'xcache' ] );
                break;
            case 'apc':
                $cache = new cache_apc( $cacheconf[ 'apc' ] );
                break;
            case 'yac':
                $cache = new cache_yac( $cacheconf[ 'yac' ] );
                break;
            default:
                return xn_error( -1, '不支持的 cache type:' . $cacheconf[ 'type' ] );
        }
        return $cache;
    }
    return NULL;
}

function cache_get( $k, $c = NULL ) {
    $cache = $_SERVER[ 'cache' ];
    $c = $c ? $c : $cache;
    if ( !$c ) return FALSE;

    strlen( $k ) > 32 AND $k = md5( $k );

    $k = $c->cachepre . $k;
    $r = $c->get( $k );
    return $r;
}

function cache_set( $k, $v, $life = 0, $c = NULL ) {
    $cache = $_SERVER[ 'cache' ];
    $c = $c ? $c : $cache;
    if ( !$c ) return FALSE;

    strlen( $k ) > 32 AND $k = md5( $k );

    $k = $c->cachepre . $k;
    $r = $c->set( $k, $v, $life );
    return $r;
}

function cache_delete( $k, $c = NULL ) {
    $cache = $_SERVER[ 'cache' ];
    $c = $c ? $c : $cache;
    if ( !$c ) return FALSE;

    strlen( $k ) > 32 AND $k = md5( $k );

    $k = $c->cachepre . $k;
    $r = $c->delete( $k );
    return $r;
}

// 尽量避免调用此方法，不会清理保存在 kv 中的数据，逐条 cache_delete() 比较保险
function cache_truncate( $c = NULL ) {
    $cache = $_SERVER[ 'cache' ];
    $c = $c ? $c : $cache;
    if ( !$c ) return FALSE;

    //$k = $c->cachepre.$k;
    $r = $c->truncate();
    return $r;
}


function sitemap( $pid ) {
    $list = '';
    $contentlist = '';
    $data = sitesort( $pid );
    foreach ( $data as $value ) {
        if ( $value[ 'num' ] > 0 ) {
            $content = sitecontent( $value[ 'sid' ] );
            foreach ( $content as $clink ) {
                $contentlist .= $clink[ 'link' ];
            }
        } else {
            $contentlist = '';
        }
        $list .= '<tr>
				<td>' . $value[ 'sid' ] . '</td>				
				<td>' . $value[ 'slink' ] . '</td>
				<td>' . $value[ 'stype' ] . '</td>
				<td>' . $value[ 'num' ] . '</td>
				<td>' . $contentlist . '</td>
			</tr>';
        if ( $value[ 'count' ] > 0 )$list .= sitemap( $value[ 'sid' ] );
    }
    return $list;
}

function sitesort( $pid ) {
    $list = array();
    $data = db_load_sql( 'select sid,s_name,s_type,s_edittime,s_url,model_name,(select count(*) from [dbpre]sort where s_pid=t.sid and (s_onoff=0 or s_onoff=1)) as c from [dbpre]model as a,[dbpre]sort t where s_type=model_type and (s_onoff=0 or s_onoff=1) and s_pid=' . $pid . ' order by s_order asc , sid asc' );
    foreach ( $data as $value ) {
        $i = $value[ 'sid' ];
        $stype = $value[ 'model_name' ];
        $outlink = $stype == 'links' ? $value[ 's_url' ] : '';
        $num = db_count( 'content', 'c_onoff=1 and c_sid=' . $i );
        $sortlink = ' <a href=' . getsortlink( $value[ 's_type' ], $i, $outlink ) . ' target="_blank">' . ( $value[ 's_name' ] ) . '</a>';
        array_push( $list, array(
            'sid' => $i,
            'slink' => $sortlink,
            'stype' => $stype,
            'num' => $num,
            'count' => $value[ 'c' ]
        ) );
    }
    return $list;
}

function sitebrand( $pid ) {
    $list = array();
    $data = db_load( 'brand', 'b_onoff=1', 'bid,b_name,b_enname,b_edittime' );
    foreach ( $data as $value ) {
        $i = $value[ 'bid' ];
        $link = ' <a href="' . getbrandlink( '', $value[ 'b_enname' ] ) . '" target="_blank">' . leftstr( $value[ 'b_name' ], 10 ) . '</a>';
        array_push( $list, array(
            'id' => $i,
            'link' => $link,
            'time' => $value[ 'b_edittime' ]
        ) );
    }
    return $list;
}

function sitecontent( $sid ) {
    $list = array();
    $data = db_load( 'content', 'c_onoff=1 and c_sid=' . $sid, 'cid,c_title,c_link,c_pagename,c_edittime' );
    foreach ( $data as $value ) {
        $i = $value[ 'cid' ];
        $link = ' <a href="' . getcontentlink( $i, $value[ 'c_pagename' ], $value[ 'c_link' ] ) . '" target="_blank">' . leftstr( $value[ 'c_title' ], 10 ) . '</a>';
        array_push( $list, array(
            'id' => $i,
            'link' => $link,
            'time' => $value[ 'c_edittime' ]
        ) );
    }
    return $list;
}
?>