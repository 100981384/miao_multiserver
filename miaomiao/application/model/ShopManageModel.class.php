<?php
/**
 * Created by PhpStorm.
 * User: 乱序
 * Date: 2018/5/4
 * Time: 10:31
 */

class ShopManageModel extends Model
{
    public function addGoods($name,$tag,$core,$memory,$price,$month,$type,$avatar,$is_sale){
        $sql = "INSERT INTO miao_shop(shop_server_name,shop_server_tag,shop_server_core,shop_server_memory,shop_server_price,shop_server_month,shop_server_type,shop_server_avatar,shop_is_sale) values ('{$name}','{$tag}',{$core},{$memory},{$price},{$month},'{$type}','{$avatar}',{$is_sale})";
        if($this->db->exec($sql)!=false){
            return true;
        }
        return false;
    }
    public function updateGoods($shopid,$name,$tag,$core,$memory,$price,$month,$type,$avatar,$is_sale){
        $sql = "UPDATE miao_shop SET shop_server_name = '{$name}',shop_server_tag = '{$tag}',shop_server_core = {$core},shop_server_memory = {$memory},shop_server_price = {$price},shop_server_month = {$month},shop_server_type = '{$type}',shop_server_avatar = '{$avatar}',shop_is_sale = {$is_sale} WHERE shopid = {$shopid}";
        if($this->db->exec($sql)!==false){
            return true;
        }
        return false;
    }
    public function upDownGoods($shopid,$switch){
        $sql = "UPDATE miao_shop SET shop_is_sale = {$switch} WHERE shopid = {$shopid}";
        if($this->db->exec($sql)!==false){
            return true;
        }
        return false;
    }
    public function deleteGoods($shopid){
        $sql = "DELETE FROM miao_shop WHERE shopid = {$shopid}";
        if($this->db->exec($sql)!=false){
            return true;
        }
        return false;
    }


}