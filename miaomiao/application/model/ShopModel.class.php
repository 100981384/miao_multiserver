<?php
/**
 * Created by PhpStorm.
 * User: ÂÒÐò
 * Date: 2018/3/16
 * Time: 21:02
 */
class ShopModel extends Model {
    public function getUserMoney($uid){
        $sql = "SELECT money FROM miao_userinfo WHERE uid={$uid}";
        return $this->db->getAll($sql)[0];
    }
    public function getShopInfo($sid){
        $sql = "SELECT * FROM miao_shop WHERE shopid={$sid}";
        if($this->db->getAll($sql)!=null){
            return $this->db->getAll($sql)[0];
        }
    }
    public function getAllShopItem(){
        $sql = "SELECT * FROM miao_shop";
        if($this->db->getAll($sql)!=null){
            return $this->db->getAll($sql)[0];
        }
    }
    public function reduceMoney($moneycount,$uid){
        $sql = "UPDATE miao_userinfo SET money = money - {$moneycount} WHERE uid = {$uid}";
        return $this->db->exec($sql);
    }
    public function alreadyBuy($uid,$shopid){
        $sql = "SELECT sid,shop_id FROM miao_serverinfo,miao_shop WHERE user_id = {$uid} AND shop_id = shopid AND shop_id = {$shopid}";
        if($this->db->getAll($sql)!=null){
            return $this->db->getAll($sql)[0];
        }
    }
    public function updateDay($sid,$day){
        $sql = "UPDATE miao_serverinfo SET server_end_day = '{$day}' WHERE sid = {$sid}";
        return $this->db->exec($sql);
    }
    public function getServerInfo($sid){
        $sql = "SELECT * FROM miao_serverinfo WHERE sid={$sid}";
        return $this->db->getAll($sql)[0];
    }
    public function getALLServer(){
        $sql = "SELECT * FROM miao_serverinfo";
        return $this->db->getAll($sql)[0];
    }
    public function addServer($shopid,$uid,$type,$endDay,$memory,$sport,$rport,$uname){
        $sql = "INSERT INTO miao_serverinfo (shop_id,user_id,server_type,server_add_day,server_end_day,server_memory,server_port,server_rcon_port,user_name) VALUES ({$shopid},{$uid},'{$type}',now(),'{$endDay}',{$memory},{$sport},{$rport},{$uname})";
        $this->db->exec($sql);
        return $this->db->getInsertId();
    }
}