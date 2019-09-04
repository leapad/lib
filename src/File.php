<?php
/**
 * 文件
 * @author Yueool
 */
namespace leapad\lib;

class File{

    /**
     * 保存文件
     * @param $filename
     * @param $data
     * @return bool
     */
    public static function saveByData($filename, $data){

        //创建保存目录
        $saveDir = dirname($filename);
        if(!file_exists($saveDir)){
            if(!mkdir ($saveDir,0777,true)){
                throw new \RuntimeException("保存目录创建失败");
            }
        }

        //保存文件
        if(!file_put_contents($filename, $data)){
            throw new \RuntimeException("HTML简历保存失败");
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