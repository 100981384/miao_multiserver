<?php
/**
 * Created by PhpStorm.
 * User: 乱序
 * Date: 2018/3/16
 * Time: 20:12
 */
class IndexModel extends  Model{
    public function getServerInfo($sid){
        $sql = "SELECT * FROM miao_serverinfo WHERE sid={$sid}";
        return $this->db->getAll($sql)[0];
    }
//    public function showOwnServer($uid){
//        $sql = "SELECT * FROM miao_serverinfo WHERE user_id = {$uid}";
//        if($this->db->getAll($sql)!=false){
//            return $this->db->getAll($sql)[0];
//        }
//    }
    public function showOwnServerInfo($uid){
        $sql = "SELECT * FROM miao_serverinfo,miao_shop WHERE shop_id= shopid AND user_id={$uid}";
        if($this->db->getAll($sql)!=false){
            return $this->db->getAll($sql)[0];
        }
    }
    public function updateJar($jarpath,$jarpublish,$sid){
        $sql = "UPDATE miao_serverinfo SET server_jarpath = '{$jarpath}',server_publishjar={$jarpublish} WHERE sid = $sid";
        return $this->db->exec($sql);
    }
}