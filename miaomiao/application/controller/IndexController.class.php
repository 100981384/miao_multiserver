<?php
class IndexController extends Controller
{
    private $Rcon;

    public function Index(){
        $indexModel = new indexModel("miao_serverinfo");
        $user = $this->getUser();
        $uid = $user['userID'];
        if($this->isAdmin()==true){
            $OwnServer = $indexModel->showAllServerInfo();
        }else{
            $OwnServer = $indexModel->showOwnServerInfo($uid);
        }
        include_once(VIEW_PATH."index.html");
    }
    //UDP通信客户端
    public function udp(){
        $this->ServerConfig($_POST['ServerId']*1);
        $msg = "{\"ServerId\":\"{$_POST['ServerId']}\",\"ServerSwitch\":{$_POST['ServerSwitch']},\"Uname\":\"{$_POST['Uname']}\"}";
        $this->udpGet($msg);
        echo $msg;
    }
    //向指定服务器发送rcon指令
    public function sendRcon(){
        $sid = $_POST['sid'];
        $command = $_POST['command'];
        $ShopModel = new ShopModel("miao_serverinfo");
        $serverInfo = $ShopModel->getServerInfo($sid)[0];
        $user = $this->getUser();
        if($serverInfo['user_id']==$user['userID']){
            $this->helper("MinecraftRcon");
            $rconPort = $serverInfo['server_rcon_port'];
            $password = $serverInfo['server_rcon_password'];
            //服务器开启状态
            try{
                $this->Rcon = new MinecraftRcon($rconPort, $password);
                $return = $this->Rcon->sendCommand($command);
                $returnArr = explode("\n",$return);
                $returnstr = "";
                for ($i=0;$i<count($returnArr);$i++){
                    $patt= "{§\w{1}}";
                    $item = preg_replace($patt,"",$returnArr[$i]);
                    $item = "<span style=\"color: lightskyblue\">$item</span>";
                        $returnstr .= $item."<br>";
                }

                if(strlen($returnstr)==125&&$command=="gc"){
                    echo "<span style=\"color: lightskyblue\">[喵喵出租屋] </span>你还没有安装Essentials基础插件！无法使用/gc指令<br>";
                }else {
                    echo $returnstr;
                }
            }
            //服务器未开启
            catch (Exception $e){
                echo "<span class='console-time'>[喵喵出租屋] </span>你的服务器还未开启,或者正在启动中,请稍后再试<br>";
            }

        }
    }
    //读取minecraft配置文件
    public function readServerConfig(){
        $sid = $_POST['sid'];
        $ShopModel = new ShopModel("miao_serverinfo");
        $serverInfo = $ShopModel->getServerInfo($sid)[0];
        $user = $this->getUser();
        $type = $serverInfo['server_type'];
        if($serverInfo['user_id']==$user['userID']||$user['role']==1) {
            $this->helper("MinecraftRcon");
            $rconPort = $serverInfo['server_rcon_port'];
            $password = $serverInfo['server_rcon_password'];
            //服务器开启
            try {
                $this->Rcon = new MinecraftRcon($rconPort, $password);
                $uname = $serverInfo['user_name'];
                if ($type == "mc") {
                    $ServerName = "MiaoMcServer" . $sid;
                }
                $path = SETUP_PATH."data\User\\$uname\home\\$ServerName\server.properties";
                //文件不存在处理
                if (!file_exists($path)) {
                    $Serverpath = SETUP_PATH."data\User\\$uname\home\\$ServerName\\";
                    $this->recurse_copy(MC_SERVER_DEFAULT, $Serverpath);
                }
                $handel = fopen($path, "r+");
                $configStr = "";
                while (!feof($handel)) {
                    $configStr .= fgets($handel);
                }
                $arr = explode("\n", $configStr);
                $newarr = array();
                for ($value = 0; $value < count($arr); $value++) {
                    if (stripos($arr[$value], "motd") !== false) {
                        //存在server-port
                        $str = iconv(mb_detect_encoding($arr[$value]),"UTF-8",$arr[$value]);
                        $newarr['motd'] = substr($str, 5);
                    }
                    if (stripos($arr[$value], "max-players") !== false) {
                        $newarr['max_players'] = substr($arr[$value], 12,strlen($arr[$value])-12);
                    }
                    if ($value == count($arr) - 1) {
                        if (stripos($configStr, "motd") === false) {
                            $newarr['motd'] = "喵喵出租屋 QQ100981384";
                        }
                        if (stripos($configStr, "max-players") === false) {
                            $newarr['max_players'] = 20;
                        }
                    }
                }
                $newarr['switch'] = $serverInfo['server_on'] * 1;
                $newarr['type'] = $serverInfo['server_type'];
                $newarr['sid'] = $sid;
                $newarr['serverjar'] = $serverInfo['server_jarpath'];
                $newarr['port'] = $serverInfo['server_port'];
                $newarr['tps'] = 0;
                $newarr['uname']=$uname;
                $newarr['jarpublish']=$serverInfo['server_publishjar'];
                if($user['role']==1){
                    $newarr['serverpath'] = str_ireplace("\\","/",SETUP_PATH."data/User/$uname/home/$ServerName/");
                }else {
                    $newarr['serverpath'] = str_ireplace("\\","/","/$ServerName/");
                }

                $player = $this->Rcon->sendCommand("list");
                //指令发送成功
                if ($player == true) {
                    if (stripos($player, "/") == true) {
                        //初始客户端
                        $sub = stripos($player, "/");
                        $endstr = substr($player, $sub - 1);
                        $end = stripos($endstr, "/");
                        $newarr['player'] = substr($player, $sub - 1, $end)*1;

                    } else if (stripos($player, "§c") == true) {

                        //essentials
                        $sub = stripos($player, " §c");
                        $endstr = stripos($player, "§6 ");
                        $newarr['player'] = substr($player, $sub + 4, $endstr - $sub - 4) * 1;
                    } else {
                        $newarr['player'] = 0;
                    }
                }
                $memory = $this->Rcon->sendCommand("gc");
                //处理gc指令
                if ($memory == true&&strlen($memory)!=40) {
                    $arr = explode("\n", $memory);
                    $sub = stripos($arr[1], "§a");
                    $newarr['tps'] = substr($arr[1], $sub + 3);
                    $sub = stripos($arr[2], "§c");

                    $str = substr($arr[2], $sub + 3, -4);
                    $arr2 = explode(",",$str);
                    $newstr = "";
                    foreach ($arr2 as $item){
                        $newstr.=$item;
                    }
                    $newarr['max_memory'] = $newstr;
                    $sub = stripos($arr[4], "§c");
                    $str = substr($arr[4], $sub + 3,-3);
                    $arr2 = explode(",",$str);
                    $newstr = "";
                    foreach ($arr2 as $item){
                        $newstr.=$item;
                    }
                    $newarr['res_memory'] = $newstr;
                    $sub = stripos($arr[0], "§c");
                    $newarr['runing_time'] = substr($arr[0], $sub + 3);
                }else {
                    $newarr['runing_time'] = 0;
                    $newarr['max_memory']=$serverInfo['server_memory'];
                    $newarr['res_memory'] = $newarr['max_memory'];
                }
            }
            //服务器未开启
            catch(Exception $e){
                $uname = $serverInfo['user_name'];
                if ($type == "mc") {
                    $ServerName = "MiaoMcServer" . $sid;
                }
                $path = SETUP_PATH."data\User\\$uname\home\\$ServerName\server.properties";
                //文件不存在处理
                if (!file_exists($path)) {
                    $Serverpath = SETUP_PATH."data\User\\$uname\home\\$ServerName\\";
                    $this->recurse_copy(MC_SERVER_DEFAULT, $Serverpath);
                }
                $handel = fopen($path, "r+");
                $configStr = "";
                while (!feof($handel)) {
                    $configStr .= fgets($handel);
                }
                $arr = explode("\n", $configStr);
                $newarr = array();
                for ($value = 0; $value < count($arr); $value++) {
                    if (stripos($arr[$value], "motd") !== false) {
                        //存在server-port
                        $newarr['motd'] = substr($arr[$value], 5);
                    }
                    if (stripos($arr[$value], "max-players") !== false) {
                        //                    echo "fuck";
                        $newarr['max_players'] = substr($arr[$value], 12,strlen($arr[$value])-12);
                    }
                    if ($value == count($arr) - 1) {
                        if (stripos($configStr, "motd") === false) {
                            $newarr['motd'] = "喵喵出租屋 QQ100981384";
                        }
                        if (stripos($configStr, "max-players") === false) {
                            $newarr['max_players'] = 20;
                        }
                    }
                }
                $newarr['switch'] = $serverInfo['server_on'] * 1;
                $newarr['type'] = $serverInfo['server_type'];
                $newarr['sid'] = $sid;
                $newarr['serverjar'] = $serverInfo['server_jarpath'];
                $newarr['port'] = $serverInfo['server_port'];
                $newarr['tps'] = 0;
                $newarr['player']=0;
                $newarr['max_memory']=$serverInfo['server_memory'];
                $newarr['res_memory']=$serverInfo['server_memory'];
                $newarr['runing_time']="服务器未开启";
                $newarr['uname']=$uname;
                $newarr['jarpublish']=$serverInfo['server_publishjar'];
                if($user['role']==1){
                    $newarr['serverpath'] = str_ireplace("\\","/",SETUP_PATH."data/User/$uname/home/$ServerName/");
                }else {
                    $newarr['serverpath'] = str_ireplace("\\","/","/$ServerName/");
                }

                exit(json_encode($newarr));
            }
            exit(json_encode($newarr));
    }
}
//读取lastet.log文件
    public function readConsole(){
        $sid = $_POST['sid'];
        $ShopModel = new ShopModel("miao_serverinfo");
        $serverInfo = $ShopModel->getServerInfo($sid)[0];
        $user = $this->getUser();
        if($user['userID']==$serverInfo['user_id']){
            $uname = $user['name'];
            $type = $serverInfo['server_type'];
            if ($type == "mc") {
                $ServerName = "MiaoMcServer" . $sid;
            }
            $path = "E:\kode\data\User\\$uname\home\\$ServerName\\";
            if(file_exists($path."logs")){
                $file = $path."logs".DS."latest.log";
                if(file_exists($file)){
                    $handel = fopen($file,"r");
                    $arr = array();
                    if(isset($_POST['ftell'])){
                        fseek($handel,$_POST['ftell']);
                    }
                    while(!feof($handel)){
                        $str = fgets($handel);
                        $str = str_replace("[Server thread/INFO]",iconv("UTF-8","GBK","[服务器信息]"),$str);
                        $patt = "{\[RCON Listener #1/INFO\].+}";
                        $patt2= "{Rcon issued server command.+}";
                        $patt3 = "{\[+\d{2}:\d{2}:\d{2}\]+}";
                        if(!preg_match($patt,$str)&&!preg_match($patt2,$str)){
                            //时间
                            $str2 = substr($str,0,10);
                            //服务器信息
                            $str3 = substr($str,10);
                            //<span>+时间
                            $str4 = preg_replace($patt3,"<span class=\"console-time\">",$str2).substr($str,0,10);
                            $arr[] = $str4."</span>&nbsp;".$str3;
                        }
                    }
                    //拼接成html
                    $html = "";
                    foreach ($arr as $item){
                        if($item!="</span>&nbsp;"){
                            $html .= iconv("GBK","UTF-8",$item)."<br>";
                        }
                    }
                    $returnarr = array(
                        "state"=>200,
                        "ftell"=>ftell($handel),
                        "consoleMsg"=>$html);
                }else {
                    $html =  "<br><br><br><br><br><br><br><br><br><br><br><h3><span class='console-time'>[喵喵出租屋]</span> 你至少开启一次服务器才能使用控制台，请稍后刷新</h3>";
                    $returnarr = array(
                        "state"=>-200,
                        "ftell"=>0,
                        "consoleMsg"=>$html
                    );
                }
            }else{
                mkdir($path."logs");
                header("location: index.php");
            }
            header("Content-Type:text/html; charset=utf-8");
            echo json_encode($returnarr);
        }
    }
    //获取能够使用的jar包
    public function usablejar(){
        $sid = $_POST['sid'];
        $ShopModel = new ShopModel("miao_serverinfo");
        $serverInfo = $ShopModel->getServerInfo($sid)[0];
        $user = $this->getUser();
        $uname = $user['name'];
        $ServerName = "MiaoMcServer".$sid;
        $serverpath = SETUP_PATH."data\User\\$uname\home\\$ServerName\\";
        $path = MC_SERVER_JAR;
        $arr = array();
        $sub = 1;
        $this->readFile($path,$arr,$sub);
        $sub = 0;
        $this->readFile($serverpath,$arr,$sub);
        $newarr = array();
        foreach ($arr as $item){
            $sub = substr($item,-1);
            $item =  iconv("UTF-8","GBK",substr($item,0,-1));
            if(filesize($item)>=10485760){
                $poi = strripos($item,"\\");
                $item = iconv("GBK","UTF-8",$item.$sub);
                $newarr[] = substr($item,$poi+1);;
            }
        }
        echo json_encode($newarr);
    }
    //读取文件
    private function readFile($path,&$arr,$sub){
        if($path!="."&&$path!=".."){
            $handel = opendir($path);
            while ($resName = readdir($handel)) {
                if($resName!="."&&$resName!=".."){
                    if(is_dir($path."/".$resName)){

                    }else{
                        $arr[] =$path.DS.$resName.$sub;
                    }
                }
            }
        }
    }
}

