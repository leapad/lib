<?php
/**
 * @author caojingfei
 * @copyright dataguru.cn
 * @version v1.0
 */

namespace leapad\lib;

class Tools{

    /**
     * @param int $length
     * @return string
     * 随机生成字符串
     */
    public static function inoncestr($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyz123456789';

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /**
     * @param $string
     * @param int $force
     * @return array|string
     * 安全过滤
     */
    public static function iaddslashes($string, $force = 1)
    {
        if (is_array($string)) {
            $keys = array_keys($string);
            foreach ($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = self::iaddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }

    /**
     * @param $int
     * @param bool $allowarray
     * @return int
     * 整形过滤
     */
    public static function iintval($int, $allowarray = false)
    {
        $ret = intval($int);
        if ($int == $ret || !$allowarray && is_array($int)) return $ret;
        if ($allowarray && is_array($int)) {
            foreach ($int as &$v) {
                $v = self::iintval($v, true);
            }
            return $int;
        } elseif ($int <= 0xffffffff) {
            $l = strlen($int);
            $m = substr($int, 0, 1) == '-' ? 1 : 0;
            if (($l - $m) === strspn($int, '0987654321', $m)) {
                return $int;
            }
        }
        return $ret;
    }

    /**
     * @param null $var
     * @param bool $vardump
     * 调试时使用
     */
    public static function ivar_dump($var = null, $vardump = true)
    {
        echo '<pre>';
        $vardump = empty($var) ? true : $vardump;
        if ($vardump) {
            var_dump($var);
        } else {
            print_r($var);
        }
        exit();
    }

    /**
     * @param $dir
     * @param int $mode
     * @param bool $makeindex
     * @return bool
     * 循环创建目录
     */
    public static function imkdir($dir, $mode = 0777, $makeindex = TRUE)
    {
        if (!is_dir($dir)) {
            self::imkdir(dirname($dir), $mode, $makeindex);
            @mkdir($dir, $mode);
            if (!empty($makeindex)) {
                @touch($dir . '/index.html');
                @chmod($dir . '/index.html', 0777);
            }
        }
        return true;
    }


    /**
     * @param $string
     * @return array|string
     * 安全过滤, 显示时去掉/
     */
    public static function istripslashes($string)
    {
        if (empty($string)) return $string;
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::istripslashes($val);
            }
        } else {
            $string = stripslashes($string);
        }
        return $string;
    }

    /**
     * @param $email
     * @return bool
     * 验证是否有邮箱地址
     */
    public static function isemail($email)
    {
        return strlen($email) > 6 && strlen($email) <= 32 && preg_match("/^([A-Za-z0-9\-_.+]+)@([A-Za-z0-9\-]+[.][A-Za-z0-9\-.]+)$/", $email);
    }

    /**
     * @param $string
     * @param $find
     * @return bool
     * 判断字符串是否包含某些字符
     */
    public static function strexists($string, $find)
    {
        return !(strpos($string, $find) === FALSE);
    }

    /**
     * @param $str
     * @return int
     * 获得字符串长度
     */
    public static function istrlen($str)
    {
        if (strtolower(CHARSET) != 'utf-8') {
            return strlen($str);
        }
        $count = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $value = ord($str[$i]);
            if ($value > 127) {
                $count++;
                if ($value >= 192 && $value <= 223) $i++;
                elseif ($value >= 224 && $value <= 239) $i = $i + 2;
                elseif ($value >= 240 && $value <= 247) $i = $i + 3;
            }
            $count++;
        }
        return $count;
    }

    /**
     * @param $string
     * @param $length
     * @param string $dot
     * @return mixed|string
     * 截取字符串
     */
    public static function cutstr($string, $length, $dot = ' ...', $charset = 'utf-8')
    {
        if (strlen($string) <= $length) {
            return $string;
        }

        $pre = chr(1);
        $end = chr(1);
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);

        $strcut = '';

        if ($charset == 'utf-8') {

            $n = $tn = $noc = 0;
            while ($n < strlen($string)) {

                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn = 2;
                    $n += 2;
                    $noc += 2;
                } elseif (224 <= $t && $t <= 239) {
                    $tn = 3;
                    $n += 3;
                    $noc += 2;
                } elseif (240 <= $t && $t <= 247) {
                    $tn = 4;
                    $n += 4;
                    $noc += 2;
                } elseif (248 <= $t && $t <= 251) {
                    $tn = 5;
                    $n += 5;
                    $noc += 2;
                } elseif ($t == 252 || $t == 253) {
                    $tn = 6;
                    $n += 6;
                    $noc += 2;
                } else {
                    $n++;
                }

                if ($noc >= $length) {
                    break;
                }

            }
            if ($noc > $length) {
                $n -= $tn;
            }

            $strcut = substr($string, 0, $n);

        } else {
            $_length = $length - 1;
            for ($i = 0; $i < $length; $i++) {
                if (ord($string[$i]) <= 127) {
                    $strcut .= $string[$i];
                } else if ($i < $_length) {
                    $strcut .= $string[$i] . $string[++$i];
                }
            }
        }

        $strcut = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

        $pos = strrpos($strcut, chr(1));
        if ($pos !== false) {
            $strcut = substr($strcut, 0, $pos);
        }
        return $strcut . $dot;
    }

    /**
     * @return bool
     * 验证是否是移动端访问
     */
    public static function ismob()
    {
        $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';

        $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
        $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

        $found_mobile = self::isubstrs($mobile_os_list, $useragent_commentsblock) ||
            self::isubstrs($mobile_token_list, $useragent);

        if ($found_mobile) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $substrs
     * @param $text
     * @return bool
     * 判断字符串是否在指定数组里
     */
    public static function isubstrs($substrs, $text)
    {
        foreach ($substrs as $substr) {
            if (false !== strpos($text, $substr)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $substrs
     * @param $text
     * @return bool
     * 验证字符串是否包含某些支付
     */
    public static function isubstr($substrs, $text)
    {
        foreach ($substrs as $substr) {
            if (false !== strpos($text, $substr)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array|false|string
     * 获得用户ip地址
     */
    public static function get_clientip()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $clientip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $clientip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $clientip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $clientip = $_SERVER['REMOTE_ADDR'];
        }
        return $clientip ? $clientip : "unknow";
    }

    /**
     * 友好的时间显示
     *
     * @param int $sTime 待显示的时间
     * @param string $type 类型. normal | mohu | full | ymd | other
     * @param string $alt 已失效
     * @return string
     */
    public static function friendlyDate($sTime, $type = 'normal', $alt = 'false')
    {
        if (!$sTime)
            return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime = time();
        $dTime = $cTime - $sTime;
        $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
        //$dDay     =   intval($dTime/3600/24);
        $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if ($type == 'normal') {
            if ($dTime < 60) {
                if ($dTime < 10) {
                    return '刚刚';    //by yangjs
                } else {
                    return intval(floor($dTime / 10) * 10) . "秒前";
                }
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
                //今天的数据.年份相同.日期相同.
            } elseif ($dYear == 0 && $dDay == 0) {
                //return intval($dTime/3600)."小时前";
                return '今天' . date('H:i', $sTime);
            } elseif ($dYear == 0) {
                return date("m月d日 H:i", $sTime);
            } else {
                return date("Y-m-d H:i", $sTime);
            }
        } elseif ($type == 'mohu') {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dDay > 0 && $dDay <= 7) {
                return intval($dDay) . "天前";
            } elseif ($dDay > 7 && $dDay <= 30) {
                return intval($dDay / 7) . '周前';
            } elseif ($dDay > 30) {
                return intval($dDay / 30) . '个月前';
            }
            //full: Y-m-d , H:i:s
        } elseif ($type == 'full') {
            return date("Y-m-d , H:i:s", $sTime);
        } elseif ($type == 'ymd') {
            return date("Y-m-d", $sTime);
        } else {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dYear == 0) {
                return date("Y-m-d H:i:s", $sTime);
            } else {
                return date("Y-m-d H:i:s", $sTime);
            }
        }
    }

    /**
     * @param $filesize
     * @return string
     * 获得文件大小
     */
    public static function getSize($filesize)
    {
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            $filesize = $filesize . ' 字节';
        }
        return $filesize;
    }

    /**
     * $string 明文或密文
     * $operation 加密ENCODE或解密DECODE
     * $key 密钥
     * $expiry 密钥有效期
     */
    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥
        $ckey_length = 4;

        $key = md5($key ? $key : 'km@dataguru!@#');

        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }


    /**
     * @param $filePath
     * @param $saveAsFileName
     * 单文件下载
     * 文件绝对地址
     * $filePath= 'https://www.test.com/file/测试.txt';
     * 获取文件名 测试.txt
     * $saveAsFileName = end(explode('/',$filePath));
     * 这里获取文件名,不可用pathinfo()函数,basename虽然一般在本地可以正常获取,但是提交阿里服务器时,有时会出现获取不到,获取不完整的情况,我遇到的是只获取到了后缀,文件名缺失
     * @return bool
     */

    public static function downloadFile($filePath, $saveAsFileName)
    {
        $filename = $filePath;
        $saveAsFileName = iconv('utf8', 'gbk', $saveAsFileName);
        $file = fopen($filename, "rb");
        Header("Content-type:  application/octet-stream ");
        Header("Accept-Ranges:  bytes ");
        Header("Content-Disposition:  attachment;  filename= {$saveAsFileName}");
        $contents = "";
        while (!feof($file)) {
            $contents .= fread($file, 8192);
        }
        echo $contents;
        fclose($file);
        return true;
    }


    /**
     * @param $filePath
     * 多文件以压缩包的形式下载
     * @return bool
     */
    public static function downLoadFiles($files, $zipName = 'KM附件打包下载')
    {

        $tmpFile = tempnam('/tmp', '');
        $zip = new \ZipArchive;
        $zip->open($tmpFile, \ZipArchive::CREATE);
        foreach ($files as $file) {
            // download file
            $fileContent = file_get_contents($file);
            $file = iconv('utf-8', 'GBK', basename($file));
            $zip->addFromString($file, $fileContent);
        }
        $zip->close();
        header('Content-Type: application/zip;charset=utf8');
        header('Content-disposition: attachment; filename=' . $zipName . '.zip');
        header('Content-Length: ' . filesize($tmpFile));
        readfile($tmpFile);
        unlink($tmpFile);

        return true;
    }


    /**
     * @param $input
     * @param $start
     * @param $end
     * @return bool|string
     * 匹配2个字符之间的字符串
     */
    public static function getNeedBetweenStr($input, $start, $end)
    {
        $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
        return $substr;
    }

    /**
     * @param $arr1
     * @param $arr2
     * @return array|bool
     * 验证2个数组是否存在交集
     */
    public static function checkHasIntersect($arr1, $arr2)
    {
        if (empty($arr1) || empty($arr2)) {
            return false;
        }
        if (array_intersect($arr1, $arr2)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $str
     * @return mixed
     * 删除空格
     */
    public static function trimall($str)
    {
        return str_replace(array(" ", "　", "\t", "\n", "\r"), array("", "", "", "", ""), $str);
    }

    /**
     * 浏览器友好的变量输出
     * @param mixed $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    public static function dump($var, $echo = true, $label = null, $strict = true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        } else
            return $output;
    }

    /**
     * @param string $url
     * @param array $post_data
     * @return bool|string
     *
     * $url = 'http://xxx.com';
     * $post_data['appid']       = '10';
     * $post_data['appkey']      = 'cmbohpffXVR03nIpkkQXaAA1Vf5nO4nQ';
     * $post_data['member_name'] = 'zsjs124';
     * $post_data['password']    = '123456';
     * $post_data['email']    = 'zsjs124@126.com';
     * //$post_data = array();
     * $res = self::request_post($url, $post_data);
     * print_r($res);
     *
     * 模拟post进行url请求
     */
    public static function request_post($url = '', $post_data = array())
    {
        if (empty($url) || empty($post_data)) {
            return false;
        }

        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);
        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

    /**
     * @param $url 访问的URL
     * @param string $post post数据(不填则为GET)
     * @param string $cookie 提交的$cookies
     * @param int $returnCookie 是否返回$cookies
     * @return bool|string
     *
     */
    public static function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        } else {
            return $data;
        }
    }

    /**
     * @param $str
     * @param string $separator
     * @param array $parm1
     * @param array $parm2
     * @return array|bool
     * 自定义字符串转数组
     */
    public static function istr2arr($str, $separator = ',', $parm1 = ['][', '[', ']'], $parm2 = ['', '', ''])
    {
        if (empty($str)) {
            return false;
        }

        return explode($separator, str_replace($parm1, $parm2, $str));
    }

    /**
     * @param $arr
     * @param string $separator
     * @param string $prefix
     * @param string $suffix
     * @return bool|string
     * 自定义数组转字符串
     */
    public static function iarr2str($arr, $separator = '][', $prefix = '[', $suffix = ']')
    {
        if (empty($arr)) {
            return false;
        }

        return $prefix . implode($separator, $arr) . $suffix;
    }

    /**
     * @param $cmd
     * @return array
     */
    public static function runcmd($cmd)
    {
        $cmd = trim($cmd);
        $status = 1;
        exec($cmd . ' 2>&1', $ret, $status);
        $result = array();
        $result['status'] = !$status;
        $result['cmd'] = $cmd;
        $result['log'] = join(PHP_EOL, $ret);
        return $result;
    }

    /*** 二维数组查找一维数组中值是否
     * @param $array
     * @param callable $callback
     * @return array
     */
    public static function array_where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param $arrays
     * @param $array
     * @return bool
     */
    public static function arrra_exist($arrays, $array)
    {
        return (bool)array_filter($arrays, function ($_array) use ($array) {
            return !array_diff($array, $_array);
        });
    }

    /**
     * @param $array
     * @param $indexs
     * @param bool $justvalsplease
     * @return bool|mixed
     */
    public static function arrray_element($array, $indexs, $justvalsplease = false)
    {
        $newarray = false;
        //verificamos el array
        if (is_array($array) && count($array) > 0) {

            //verify indexs and get # of indexs
            if (is_array($indexs) && count($indexs) > 0) $ninds = count($indexs);
            else return false;

            //search for coincidences
            foreach (array_keys($array) as $key) {

                //index value coincidence counter.
                $count = 0;

                //for each index we search
                foreach ($indexs as $indx => $val) {

                    //if index value is equal then counts
                    if ($array[$key][$indx] == $val) {
                        $count++;
                    }
                }
                //if indexes match, we get the array elements :)
                if ($count == $ninds) {

                    //if you only need the vals of the first coincidence
                    //witch was my case by the way...
                    if ($justvalsplease) return $array[$key];
                    else $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }


}