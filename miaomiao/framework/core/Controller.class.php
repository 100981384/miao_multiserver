<?php
class Controller
{
    //加载工具类函数
    public static function library($lib){
        include LIBRARY_PATH . "{$lib}.class.php";
    }
    //加载辅助函数
    public static function helper($helper){
        include HELPER_PATH . "{$helper}.class.php";
    }
    public static function udpGet($sendMsg = '', $ip = '127.0.0.1', $port = '6666')
    {
        $handle = stream_socket_client("udp://{$ip}:{$port}", $errno, $errstr);
        if (!$handle) {
            die("ERROR: {$errno} - {$errstr}\n");
        }
        fwrite($handle, $sendMsg);
        fclose($handle);

    }
    //解析kodexplorer的session
    public function getUser(){
        $uCookie=$_COOKIE[KOD_COOKIE];
        $file = SETUP_PATH."data/session/sess_".$uCookie;
        $str = file_get_contents($file);
        $str = substr($str,16);
        $str = unserialize($str);
        return $str;
    }
    //判断当前用户是不是管理员
    public function isAdmin(){
        $uCookie=$_COOKIE[KOD_COOKIE];
        $file = SETUP_PATH."data/session/sess_".$uCookie;
        $str = file_get_contents($file);
        $str = substr($str,16);
        $str = unserialize($str);
        if($str['role']==1){
            return true;
        }else {
            return false;
        }
    }
    public function createServerPath($uname,$sid,$type){
        if($type=="mc"){
            $path = SETUP_PATH."data".DS."User".DS."{$uname}".DS."home".DS."MiaoMcServer{$sid}";
            mkdir($path);
            return $path;
        }
    }
    //复制目录下的所有文件到目标目录
    public function recurse_copy($src,$dst) {  // 原目录，复制到的目录
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }



}
