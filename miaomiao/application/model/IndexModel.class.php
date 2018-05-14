<?php
/**
 * Created by PhpStorm.
 * User: 乱序
 * Date: 2018/3/16
 * Time: 20:12
 */
class IndexModel extends  Model{
    //获取属于自己的服务器信息
    public function getServerInfo($sid){
        $sql = "SELECT * FROM miao_serverinfo WHERE sid={$sid}";
        return $this->db->getRow($sql);
    }
    //展示属于自己的服务器信息+商店信息
    public function showOwnServerInfo($uid){
        $sql = "SELECT * FROM miao_serverinfo,miao_shop WHERE shop_id= shopid AND user_id={$uid}";
        if($this->db->getAll($sql)!=false){
            return $this->db->getRow($sql);
        }
    }
    public function showAllServerInfo(){
        $sql = "SELECT * FROM miao_serverinfo,miao_shop WHERE shop_id= shopid";
        if($this->db->getAll($sql)!=false){
            return $this->db->getRow($sql);
        }
    }

    /**
     * @param $jarpath //新的jar包地址
     * @param $jarpublish //是否使用公共jar包
     * @param $sid    //指定服务器
     * @return int
     */

    public function updateJar($jarpath,$jarpublish,$sid){
        $sql = "UPDATE miao_serverinfo SET server_jarpath = '{$jarpath}',server_publishjar={$jarpublish} WHERE sid = $sid";
        return $this->db->exec($sql);
    }
}