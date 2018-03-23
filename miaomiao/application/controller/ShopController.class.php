<?php
class ShopController extends Controller 
{
    public function Index()
    {
        $ShopModel = new ShopModel("miao_userinfo");
        $user = $this->getUser();
        $money = $ShopModel->getUserMoney($user['userID'])[0]['money'];
        $shopInfo = $ShopModel->getAllShopItem();
//        var_dump($shopInfo);
        include(VIEW_PATH."shop.html");
    }
    private function getPort(){
        $first = mt_rand(1,6);
        $second = mt_rand(0,9);
        $third = mt_rand(0,9);
        $four = mt_rand(0,9);
        $fire = mt_rand(0,9);
        $port = $first.$second.$third.$four.$fire;
        $array = array();
        $ShopModel = new ShopModel("miao_serverinfo");
        $ShopArr = $ShopModel->getALLServer();
//        var_dump($ShopArr);
        foreach ($ShopArr as $item){
            $array[]=$item['server_port'];
            $array[]=$item['server_rcon_port'];
        }
        if(in_array($port,$array)){
            $this->getPort();
        }else{
            return $port;
        }
    }
    public function buy(){
        $shopid =  $_POST['shopid'];
        $shopid = 2;
        $ShopModel = new ShopModel("miao_userinfo");
        $buyItem = $ShopModel->getShopInfo($shopid)[0];
        //获取用户信息
        $user = $this->getUser();
        $uid = $user['userID'];
        //获取用户货币数量
        $money = $ShopModel->getUserMoney($uid)[0]['money'];
        //购买服务器的月份
        $month = $buyItem['shop_server_month'];
        if($money>=$buyItem['shop_server_price']){
            //减去所需货币
            $ShopModel->reduceMoney($buyItem['shop_server_price'],$uid);
            //判断是否续费操作
            $alreadyBuyArr = $ShopModel->alreadyBuy($uid,$shopid)[0];
            if($alreadyBuyArr!=false){
                //续费
                $sid = $alreadyBuyArr['sid'];
                $ServerInfo = $ShopModel->getServerInfo($sid)[0];
                //计算一月后的时间
                $endDay = $ServerInfo['server_end_day'];
                $newEndDay = date("Y-m-d",strtotime("+{$month} Month",strtotime($endDay)));
                //更新服务器到期时间
                $ShopModel->updateDay($sid,$newEndDay);
                echo "true";
            }else{
                //购买新的服务器
                $ShopInfo = $ShopModel->getShopInfo($shopid)[0];
                $shopid = $ShopInfo['shopid'];
                $endDay = date("Y-m-d",strtotime("+{$month} Month",strtotime(date("Y-m-d"))));
                $memory = $ShopInfo['shop_server_memory'];
                $server_type = $ShopInfo['shop_server_type'];
                $server_port = $this->getPort();
                $server_rcon_port = $this->getPort();
                $addServer = $ShopModel->addServer($shopid,$uid,$server_type,$endDay,$memory,$server_port,$server_rcon_port,$user['name']);
                //初始化新服务器
                //在用户的kod目录创建服务器文件夹
                $serverPath = $this->createServerPath($user['name'],$addServer,$server_type);
                //复制服务器基本文件
                $this->recurse_copy(MC_SERVER_DEFAULT,$serverPath);
                //初始化server.properties文件
                echo "true";
            }
        }else{
            //钱不足够
            echo "false";
        }
    }
    public function ServerConfig(){
        $sid = $_POST['sid'];
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
        if($serverInfo['user_id']==$user['userID']){
            $uname = $user['name'];
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