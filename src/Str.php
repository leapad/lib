<?php
/**
 * 常用字符串函数
 * @author Yue
 */
namespace leapad\lib;

class Str{

    /**
     * 随机字符串
     * @param $len 生成字符串的长度
     * @param string $pattern 生成字符串体,默认数字+大写字母
     * @return string
     */
    function rand($len, $pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ"){
        $randstr = "";
        for($i = 0; $i < $len; $i++) {
            $randstr .= $pattern[mt_rand(0,(strlen($pattern)-1))];
        }
        return $randstr;
    }

}

?>