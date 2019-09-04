<?php
/**
 * 抓取器
 * @author Yueool
 */
namespace leapad\lib;

class Collector{

    /**
     * 获得原始文本
     * @param $url
     * @return bool|null|string|string[]
     */
    public static function getText($url){
        $text = file_get_contents($url);
        $text = preg_replace('/[\r\n]/', '', $text);//去换行
        $text = preg_replace('/\s{2,}/', ' ', $text);//多个空格换成一个空格
        return $text;
    }

    /**
     * 匹配出TITLE
     * @param $text
     * @return string
     */
    public static function getTitle($text){
        preg_match('/\<title\>(.*?)\<\/title\>/', $text, $matches);
        $title = isset($matches[1]) ? $matches[1] : "";
        $title = explode("-", $title)[0];
        return $title;
    }

    /**
     * 去掉注释
     * @param $content
     * @return null|string|string[]
     */
    public static function dislodgeAnnotation($content){
        $pattern = '/\<\!--.*?--\>/i';
        $content = preg_replace($pattern, '', $content);
        return $content;
    }

    /**
     * 保存网络文件到硬盘
     * @param $urlFile
     * @param $saveFile
     * @return bool
     */
    public static function saveUrlFile($urlFile, $saveFile){

        //参数判断
        if(empty($urlFile)){
            throw new \RuntimeException("URL文件参数不能为空");
            return false;
        }

        //资源判断
        $file = file_get_contents($urlFile);
        if(empty($file)){
            throw new \RuntimeException("得不到网络资源");
            return false;
        }

        //判断目录
        $saveDir = dirname($saveFile);
        if(empty($saveDir)){
            throw new \RuntimeException("无法从保存文件中获取保存目录");
            return false;
        }

        //创建保存目录
        if(!file_exists($saveDir)){
            if(!mkdir ($saveDir,0777,true)){
                throw new \RuntimeException("保存目录创建失败");
            }
        }

        //判断目录是否可写
        if(!is_writable($saveDir)){
            throw new \RuntimeException("目录不可写");
            return false;
        }

        //保存文件
        if(!file_put_contents($saveFile, $file)){
            throw new \RuntimeException("文件保存失败");
            return false;
        }

        return true;
    }

    /**
     * 随机字符串
     * @param $len
     * @return string
     */
    public static function randStr($len){
        $pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randkey = "";
        for($i = 0; $i< $len; $i++) {
            $randkey .= $pattern[mt_rand(0,35)];
        }
        return $randkey;
    }

}
