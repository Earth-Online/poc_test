<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2017 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class GeetestLib {
    const GT_SDK_VERSION = 'php_3.2.0';

    public static $connectTimeout = 1;
    public static $socketTimeout  = 1;

    private $response;

    public function __construct($captcha_id, $private_key) {
        $this->captcha_id  = $captcha_id;
        $this->private_key = $private_key;
    }

    public function pre_process($user_id = null) {
        $url = "http://api.geetest.com/register.php?gt=" . $this->captcha_id;
        if (($user_id != null) and (is_string($user_id))) {
            $url = $url . "&user_id=" . $user_id;
        }
        $challenge = $this->send_request($url);

        if (strlen($challenge) != 32) {
            $this->failback_process();

            return 0;
        }
        $this->success_process($challenge);

        return 1;
    }

    private function success_process($challenge) {
        $challenge      = md5($challenge . $this->private_key);
        $result         = array(
            'success'   => 1,
            'gt'        => $this->captcha_id,
            'challenge' => $challenge
        );
        $this->response = $result;
    }

    private function failback_process() {
        $rnd1           = md5(rand(0, 100));
        $rnd2           = md5(rand(0, 100));
        $challenge      = $rnd1 . substr($rnd2, 0, 2);
        $result         = array(
            'success'   => 0,
            'gt'        => $this->captcha_id,
            'challenge' => $challenge
        );
        $this->response = $result;
    }

    public function get_response_str() {
        return json_encode($this->response);
    }

    public function get_response() {
        return $this->response;
    }

    public function success_validate($challenge, $validate, $seccode, $user_id = null) {
        if (!$this->check_validate($challenge, $validate)) {
            return 0;
        }
        $data = array(
            "seccode" => $seccode,
            "sdk"     => self::GT_SDK_VERSION,
        );
        if (($user_id != null) and (is_string($user_id))) {
            $data["user_id"] = $user_id;
        }
        $url          = "http://api.geetest.com/validate.php";
        $codevalidate = $this->post_request($url, $data);
        if ($codevalidate == md5($seccode)) {
            return 1;
        } else {
            if ($codevalidate == "false") {
                return 0;
            } else {
                return 0;
            }
        }
    }

    public function fail_validate($challenge, $validate, $seccode) {
        if ($validate) {
            $value   = explode("_", $validate);
            $ans     = $this->decode_response($challenge, $value['0']);
            $bg_idx  = $this->decode_response($challenge, $value['1']);
            $grp_idx = $this->decode_response($challenge, $value['2']);
            $x_pos   = $this->get_failback_pic_ans($bg_idx, $grp_idx);
            $answer  = abs($ans - $x_pos);
            if ($answer < 4) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }

    private function check_validate($challenge, $validate) {
        if (strlen($validate) != 32) {
            return false;
        }
        if (md5($this->private_key . 'geetest' . $challenge) != $validate) {
            return false;
        }

        return true;
    }

    private function send_request($url) {

        if (function_exists('curl_exec')) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$socketTimeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                $err = sprintf("curl[%s] error[%s]", $url, curl_errno($ch) . ':' . curl_error($ch));
                $this->triggerError($err);
            }

            curl_close($ch);
        } else {
            $opts    = array(
                'http' => array(
                    'method'  => "GET",
                    'timeout' => self::$connectTimeout + self::$socketTimeout,
                )
            );
            $context = stream_context_create($opts);
            $data    = file_get_contents($url, false, $context);
        }

        return $data;
    }

    private function post_request($url, $postdata = '') {
        if (!$postdata) {
            return false;
        }

        $data = http_build_query($postdata);
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$socketTimeout);

            if (!$postdata) {
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                $err = sprintf("curl[%s] error[%s]", $url, curl_errno($ch) . ':' . curl_error($ch));
                $this->triggerError($err);
            }

            curl_close($ch);
        } else {
            if ($postdata) {
                $opts    = array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . strlen($data) . "\r\n",
                        'content' => $data,
                        'timeout' => self::$connectTimeout + self::$socketTimeout
                    )
                );
                $context = stream_context_create($opts);
                $data    = file_get_contents($url, false, $context);
            }
        }

        return $data;
    }

    private function decode_response($challenge, $string) {
        if (strlen($string) > 100) {
            return 0;
        }
        $key             = array();
        $chongfu         = array();
        $shuzi           = array("0" => 1, "1" => 2, "2" => 5, "3" => 10, "4" => 50);
        $count           = 0;
        $res             = 0;
        $array_challenge = str_split($challenge);
        $array_value     = str_split($string);
        for ($i = 0; $i < strlen($challenge); $i++) {
            $item = $array_challenge[$i];
            if (in_array($item, $chongfu)) {
                continue;
            } else {
                $value = $shuzi[$count % 5];
                array_push($chongfu, $item);
                $count++;
                $key[$item] = $value;
            }
        }

        for ($j = 0; $j < strlen($string); $j++) {
            $res += $key[$array_value[$j]];
        }
        $res = $res - $this->decodeRandBase($challenge);

        return $res;
    }

    private function get_x_pos_from_str($x_str) {
        if (strlen($x_str) != 5) {
            return 0;
        }
        $sum_val   = 0;
        $x_pos_sup = 200;
        $sum_val   = base_convert($x_str, 16, 10);
        $result    = $sum_val % $x_pos_sup;
        $result    = ($result < 40) ? 40 : $result;

        return $result;
    }

    private function get_failback_pic_ans($full_bg_index, $img_grp_index) {
        $full_bg_name = substr(md5($full_bg_index), 0, 9);
        $bg_name      = substr(md5($img_grp_index), 10, 9);

        $answer_decode = "";
        for ($i = 0; $i < 9; $i++) {
            if ($i % 2 == 0) {
                $answer_decode = $answer_decode . $full_bg_name[$i];
            } elseif ($i % 2 == 1) {
                $answer_decode = $answer_decode . $bg_name[$i];
            }
        }
        $x_decode = substr($answer_decode, 4, 5);
        $x_pos    = $this->get_x_pos_from_str($x_decode);

        return $x_pos;
    }

    private function decodeRandBase($challenge) {
        $base      = substr($challenge, 32, 2);
        $tempArray = array();
        for ($i = 0; $i < strlen($base); $i++) {
            $tempAscii = ord($base[$i]);
            $result    = ($tempAscii > 57) ? ($tempAscii - 87) : ($tempAscii - 48);
            array_push($tempArray, $result);
        }
        $decodeRes = $tempArray['0'] * 36 + $tempArray['1'];

        return $decodeRes;
    }

    private function triggerError($err) {
        trigger_error($err);
    }
}
