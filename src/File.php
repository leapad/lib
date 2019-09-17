<?php
/**
 * 文件
 * @author Yueool
 */
namespace leapad\lib;

class File{

    /**
     * 保存文件
     * 可自动创建目录
     * @param $saveFile 要保存文件的完整路径，包含文件名
     * @param $data 可以是二进制，也可以是文本
     * @return bool
     * @author Yue
     * @date 2019 09 17
     */
    public static function saveByData($saveFile, $data){

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
                return false;
            }
        }

        //判断目录是否可写
        if(!is_writable($saveDir)){
            throw new \RuntimeException("目录不可写");
            return false;
        }

        //保存文件
        if(!file_put_contents($saveFile, $data)){
            throw new \RuntimeException("文件保存失败");
            return false;
        }

        return true;
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
     * 下载文件
     * 有的时候下载文件需要登录状态，本函数便是为此服务。把登录状态通过COOKIE传过来即可,若不传只能下载不需要身份认证的普通文件
     * @author YUE
     * @param $urlFile 网络文件URL
     * @param $saveFile 保存文件
     * @param string $cookieStr cookies的字符串，例如 'id=1;loginUid=5;password=abc'
     * @return bool
     */
    public static function download($urlFile, $saveFile, $cookieStr = ''){

        //参数判断
        if(empty($urlFile)){
            throw new \RuntimeException("URL文件参数不能为空");
            return false;
        }

        //读取资源
        if(!empty($cookieStr)){
            $opts = ['http' => ['header'=> 'Cookie:'.@$cookieStr.'']];
            $context = stream_context_create($opts);
            $file = file_get_contents($urlFile, false, $context);
        }else{
            $file = file_get_contents($urlFile);
        }

        //判断资源
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
