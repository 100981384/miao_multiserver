<?php

/**
 * Created by PhpStorm.
 * User: 乱序
 * Date: 2018/5/3
 * Time: 10:58
 */
class UpFile
{
    public function upImgFile($file,$dir,$fileCount=1,$allowSub = ["jpg", "png", "jpeg", "gif"]){
        if ($fileCount == 1) {
            $sub = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (in_array($sub, $allowSub)) {
                $fileName = uniqid() . ".{$sub}";
                if (move_uploaded_file($file['tmp_name'], $dir . $fileName) != false) {
                    return $fileName;
                }else {
                    return false;
                }
            }
        }else {
            for ($i = 0; $i < $fileCount; $i++) {
                $sub = pathinfo($file['name'][$i], PATHINFO_EXTENSION);
                if (in_array($sub, $allowSub)) {
                    $fileName = md5(uniqid(microtime(true), true)) . ".{$sub}";
                    if (move_uploaded_file($file['tmp_name'][$i], $dir . $fileName) != true) {
                        return false;
                    }
                } else {
                    if(empty($file['name'][$i])==false){
                        return false;
                    }
                }
            }
        }
    }
}