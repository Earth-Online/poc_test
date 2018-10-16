<?php
/**
 *���ܣ�֧�����ӿڹ��ú���
 *��ϸ����ҳ��������֪ͨ���������ļ������õĹ��ú������Ĵ����ļ�������Ҫ�޸�
 *�汾��3.1
 *�޸����ڣ�2010-08-05
 '˵����
 '���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
 '�ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���

*/
 
/**����ǩ�����
 *$arrayҪ���ܵ�����
 *return ǩ������ַ���
*/
function build_mysign($sort_array,$security_code,$sign_type = "MD5") {
    $prestr = create_linkstring($sort_array);     	//����������Ԫ�أ����ա�����=����ֵ����ģʽ�á�&���ַ�ƴ�ӳ��ַ���
    $prestr = $prestr.$security_code;				//��ƴ�Ӻ���ַ������밲ȫУ����ֱ����������
    $mysgin = sign($prestr,$sign_type);			    //�����յ��ַ������ܣ����ǩ�����
    return $mysgin;
}	

/********************************************************************************/

/**����������Ԫ�أ����ա�����=����ֵ����ģʽ�á�&���ַ�ƴ�ӳ��ַ���
	*$array ��Ҫƴ�ӵ�����
	*return ƴ������Ժ���ַ���
*/
function create_linkstring($array) {
    $arg  = "";
    while (list ($key, $val) = each ($array)) {
        $arg.=$key."=".$val."&";
    }
    $arg = substr($arg,0,count($arg)-2);		     //ȥ�����һ��&�ַ�
    return $arg;
}

/********************************************************************************/


/**����������Ԫ�أ����ա�����=����ֵ����ģʽ�á�&���ַ�ƴ�ӳ��ַ���
 **ʹ�ó�����GET��ʽ����ʱ����URL�����Ľ��б���
	*$array ��Ҫƴ�ӵ�����
	*return ƴ������Ժ���ַ���
*/
function create_linkstring_urlencode($array) {
    $arg  = "";
    while (list ($key, $val) = each ($array)) {
		if ($key != "service" && $key != "_input_charset")
			$arg.=$key."=".urlencode($val)."&";
		else $arg.=$key."=".$val."&";
    }
    $arg = substr($arg,0,count($arg)-2);		     //ȥ�����һ��&�ַ�
    return $arg;
}

/********************************************************************************/

/**��ȥ�����еĿ�ֵ��ǩ������
	*$parameter ���ܲ�����
	*return ȥ����ֵ��ǩ����������¼��ܲ�����
 */
function para_filter($parameter) {
	
    $para = array();
	foreach($parameter as $key=>$value){
		if($key == "sign" || $key == "sign_type" || $value == ""){
			continue;
		}else{
			$para[$key] = $parameter[$key];
			
		}
	
	}
   
    return $para;
}

/********************************************************************************/

/**����������
	*$array ����ǰ������
	*return ����������
 */
function arg_sort($array) {
    ksort($array);
    reset($array);
    return $array;
}

/********************************************************************************/

/**�����ַ���
	*$prestr ��Ҫ���ܵ��ַ���
	*return ���ܽ��
 */
function sign($prestr,$sign_type) {
    $sign='';
    if($sign_type == 'MD5') {
        $sign = md5($prestr);
    }elseif($sign_type =='DSA') {
        //DSA ǩ����������������
        die("DSA ǩ����������������������ʹ��MD5ǩ����ʽ");
    }else {
        die("֧�����ݲ�֧��".$sign_type."���͵�ǩ����ʽ");
    }
    return $sign;
}

/********************************************************************************/

// ��־��Ϣ,��֧�������صĲ�����¼����
function  log_result($word) {
    $fp = fopen("log.txt","a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"ִ�����ڣ�".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}	

/********************************************************************************/

/**ʵ�ֶ����ַ����뷽ʽ
	*$input ��Ҫ������ַ���
	*$_output_charset ����ı����ʽ
	*$_input_charset ����ı����ʽ
	*return �������ַ���
 */
function charset_encode($input,$_output_charset ,$_input_charset) {
    $output = "";
    if(!isset($_output_charset) )$_output_charset  = $_input_charset;
    if($_input_charset == $_output_charset || $input ==null ) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
    } elseif(function_exists("iconv")) {
        $output = iconv($_input_charset,$_output_charset,$input);
    } else die("sorry, you have no libs support for charset change.");
    return $output;
}

/********************************************************************************/

/**ʵ�ֶ����ַ����뷽ʽ
	*$input ��Ҫ������ַ���
	*$_output_charset ����Ľ����ʽ
	*$_input_charset ����Ľ����ʽ
	*return �������ַ���
 */
function charset_decode($input,$_input_charset ,$_output_charset) {
    $output = "";
    if(!isset($_input_charset) )$_input_charset  = $_input_charset ;
    if($_input_charset == $_output_charset || $input ==null ) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
    } elseif(function_exists("iconv")) {
        $output = iconv($_input_charset,$_output_charset,$input);
    } else die("sorry, you have no libs support for charset changes.");
    return $output;
}

/*********************************************************************************/

/**���ڷ����㣬���ýӿ�query_timestamp����ȡʱ����Ĵ�������
ע�⣺���ڵͰ汾��PHP���û�����֧��Զ��XML��������˱�������������ص�����װ�и߰汾��PHP���û��������鱾�ص���ʱʹ��PHP��������
*$partner ����������ID
*return ʱ����ַ���
*/
function query_timestamp($partner) {
    $URL = "https://mapi.alipay.com/gateway.do?service=query_timestamp&partner=".$partner;
	$encrypt_key = "";
//��Ҫʹ�÷����㣬��ȡ�������4��ע��
//    $doc = new DOMDocument();
//    $doc->load($URL);
//    $itemEncrypt_key = $doc->getElementsByTagName( "encrypt_key" );
//    $encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
    return $encrypt_key;
}

?>