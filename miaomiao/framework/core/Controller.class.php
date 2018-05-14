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
    //服务器配置文件
    public function ServerConfig($sid=0){
        if($sid==0){
            $sid = $_POST['sid'];
        }
        $ShopModel = new ShopModel("miao_serverinfo");
        $serverInfo =$ShopModel->getServerInfo($sid)[0];
        if(isset($_POST['jar'])){
            if($serverInfo['server_jarpath']!=$_POST['jar']){
                $indexModel = new IndexModel("miao_serverinfo");
                $indexModel->updateJar($_POST['jar'],$_POST['jarpublish'],$sid);
            }
        }
        $sname = "喵喵出租屋 QQ100981384";
        $playerCount = 20;
        if(isset($_POST['sname'])){
            $sname = $_POST['sname'];
        }
        if(isset($_POST['playerCount'])){
            $playerCount = $_POST['playerCount'];
        }
        $user = $this->getUser();

        $type = $serverInfo['server_type'];
        if($serverInfo['user_id']==$user['userID']||$this->isAdmin()==1){
            $uname = $serverInfo['user_name'];
            if($type=="mc"){
                $ServerName = "MiaoMcServer".$sid;
            }
            $path = "E:\kode\data\User\\$uname\home\\$ServerName\server.properties";
            //文件不存在处理
            if(!file_exists($path)){
                $Serverpath = "E:\kode\data\User\\$uname\home\\$ServerName\\";
                $this->recurse_copy(MC_SERVER_DEFAULT,$Serverpath);
            }
            //读取server.properties
            $handel = fopen($path,"r+");
            $configStr = "";
            while(!feof($handel)){
                $configStr .= fgets($handel);
            }
            $arr = explode("\n",$configStr);
            //获取服务器信息
            //拆分成数组并且强行设置成数据库信息
            for ($value = 0;$value<count($arr);$value++){
                if(stripos($arr[$value],"server-port")!==false){
                    //存在server-port
                    $arr[$value] = "server-port=".$serverInfo['server_port'];
                }
                if(stripos($arr[$value],"enable-rcon")!==false){
                    //存在enable-rcon
                    $arr[$value] = "enable-rcon=true";
                }
                if(stripos($arr[$value],"rcon.port")!==false){
                    //存在rcon.port
                    $arr[$value] = "rcon.port=".$serverInfo['server_rcon_port'];
                }
                if(stripos($arr[$value],"rcon.password")!==false){
                    //存在rcon.password
                    $arr[$value] = "rcon.password=".$serverInfo['server_rcon_password'];
                }
                if(stripos($arr[$value],"max-players")!==false){
                    //存在rcon.password
                    $arr[$value] = "max-players=".$playerCount;
                }
                if(stripos($arr[$value],"motd")!==false){
                    //存在rcon.password
                    $arr[$value] = "motd=".$sname;
                }
                if(stripos($arr[$value],"\n")!==false){
                    unset($arr[$value]);
                }
                if($value==count($arr)-1){
                    if(stripos($configStr,"server-port")===false){
                        $arr[] = "server-port=".$serverInfo['server_port'];
                    }
                    if(stripos($configStr,"enable-rcon")===false){
                        $arr[]= "enable-rcon=true";
                    }
                    if(stripos($configStr,"rcon.port")===false){
                        $arr[] = "rcon.port=".$serverInfo['server_rcon_port'];
                    }
                    if(stripos($configStr,"rcon.password")===false){
                        $arr[] = "rcon.password=".$serverInfo['server_rcon_password'];
                    }
                    if(stripos($configStr,"max-players")===false){
                        $arr[] = "max-players=".$playerCount;
                    }
                    if(stripos($configStr,"motd")===false){
                        //存在rcon.password
                        $arr[] = "motd=".$sname;
                    }
                    break;
                }
            }
            $finalConfig = "";
            $arr = array_values($arr);
            for ($i = 0 ;$i<count($arr);$i++){
                if($arr[$i]==""){

                }
                else if($i!=count($arr)-1){
                    $finalConfig.=$arr[$i]."\n";
                }else {
                    $finalConfig.=$arr[$i];
                }
            }
            //完成文件初始化，覆盖服务器配置文件
            $handel = fopen($path,"w+");
            fwrite($handel,$finalConfig);
        }
    }



}
