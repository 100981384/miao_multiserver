<?php
/**
 * Created by PhpStorm.
 * User: ����
 * Date: 2018/3/16
 * Time: 21:02
 */
class ShopModel extends Model {

//根据uid获取用户货币
    public function getUserMoney($uid){
        $sql = "SELECT money FROM miao_userinfo WHERE uid={$uid}";
        return $this->db->getRow($sql);
    }
//根据服务器id获取商店信息
    public function getShopInfo($sid){
        $sql = "SELECT * FROM miao_shop WHERE shopid={$sid}";
        if($this->db->getRow($sql)!=null){
            return $this->db->getRow($sql);
        }
    }
    //获取所有商店商品
    public function getAllShopItem(){
        $sql = "SELECT * FROM miao_shop";
        if($this->db->getRow($sql)!=null){
            return $this->db->getRow($sql);
        }
    }
    public function getAllSaleItem(){
        $sql = "SELECT * FROM miao_shop WHERE shop_is_sale = 1";
        if($this->db->getRow($sql)!=null){
            return $this->db->getRow($sql);
        }
    }

    /**
     * 根据uid减少用户货币
     * @param $moneycount 减少的金钱
     * @param $uid  用户id
     * @return int   是否成功
     */
    public function reduceMoney($moneycount,$uid){
        $sql = "UPDATE miao_userinfo SET money = money - {$moneycount} WHERE uid = {$uid}";
        return $this->db->exec($sql);
    }
    //判断用户是否已经购买过这个商品
    public function alreadyBuy($uid,$shopid){
        $sql = "SELECT sid,shop_id FROM miao_serverinfo,miao_shop WHERE user_id = {$uid} AND shop_id = shopid AND shop_id = {$shopid}";
        if($this->db->getRow($sql)!=null){
            return $this->db->getRow($sql);
        }
    }
    //更新天数
    public function updateDay($sid,$day){
        $sql = "UPDATE miao_serverinfo SET server_end_day = '{$day}' WHERE sid = {$sid}";
        return $this->db->exec($sql);
    }
    //获取指定的服务器信息
    public function getServerInfo($sid){
        $sql = "SELECT * FROM miao_serverinfo WHERE sid={$sid}";
        return $this->db->getRow($sql);
    }
    //获取所有服务器信息
    public function getALLServer(){
        $sql = "SELECT * FROM miao_serverinfo";
        return $this->db->getRow($sql);
    }
    /**增加新的服务器
     * @param $shopid 商品id
     * @param $uid  用户id
     * @param $type  商品类型
     * @param $endDay  到期日期
     * @param $memory  内存 (以mb为单位)
     * @param $sport  服务器端口
     * @param $rport  rcon端口
     * @param $uname  kod面板用户名
     * @return string
     */
    public function addServer($shopid,$uid,$type,$endDay,$memory,$sport,$rport,$uname){
        $sql = "INSERT INTO miao_serverinfo (shop_id,user_id,server_type,server_add_day,server_end_day,server_memory,server_port,server_rcon_port,user_name) VALUES ({$shopid},{$uid},'{$type}',now(),'{$endDay}',{$memory},{$sport},{$rport},'{$uname}')";
        $this->db->exec($sql);
        return $this->db->getInsertId();
    }
}