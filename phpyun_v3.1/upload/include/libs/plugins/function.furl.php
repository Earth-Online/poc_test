<?php

function smarty_function_furl($paramer,&$smarty){
		global $config;
		if(empty($paramer[con])){
			$con='index';
		}else{
			$con=$paramer[con];
		}
		if(empty($paramer['m'])){
			$m='index';
		}else{
			$m=$paramer['m'];
		}
		if(!empty($paramer[url])){
			$paramers = @explode(",",$paramer[url]);
		}
		if($config['sy_seo_rewrite']){
			if($con!='index' && !empty($con)){
				$urlarr['con']=str_replace('_','',str_replace('-','',$con));
			}
			if($m!='index' && !empty($m)){
				$urlarr['m']=str_replace('_','',str_replace('-','',$m));
			}
			if($paramers){
				$p='';
				foreach($paramers as $v){
					if(!empty($v)){
						$url_info = @explode(":",$v);
					$urlarr[$url_info[0]]=str_replace('_','',str_replace('-','',$url_info[1]));
					}
				}
			}
			if($urlarr){
				foreach($urlarr as $k=>$v){
					$a[]=$k.'_'.$v;
				}
				$urltemp=implode('-',$a);
				$url.=$urltemp.'.html';
				$url="friend-".$url;
			}else{
				$url="friend-index.html";
			}

		}else{
			if($con=='index' && $m=='index'){
				$url.='index.php';
			}elseif($con=='index'){
				$url.='index.php?m='.$m;
			}elseif($m=='index'){
				$url.='index.php?con='.$con;
			}else{
				$url.='index.php?con='.$con.'&m='.$m;
			}
			if($paramers){
				$p='';
				foreach($paramers as $v){
					if(!empty($v)){
					$url_info = @explode(":",$v);
					$p.='&'.$url_info[0].'='.$url_info[1];
					}
				}
				if(strpos($url,'?')){
					$url.=$p;
				}else{
					$url.='?'.substr($p,1);
				}
			}
			$url="friend/".$url;
		}
		$url=$config[sy_weburl]."/".$url;
		return $url;
	}
?>