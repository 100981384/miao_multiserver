<?php
class ShopController extends Controller 
{
    public function Index()
    {
        $ShopModel = new ShopModel("miao_userinfo");
        $user = $this->getUser();
        $money = $ShopModel->getUserMoney($user['userID'])[0]['money'];
        $shopInfo = $ShopModel->getAllSaleItem();
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
}