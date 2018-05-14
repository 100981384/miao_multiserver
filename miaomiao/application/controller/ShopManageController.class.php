<?php

/**
 * Created by PhpStorm.
 * User: 乱序
 * Date: 2018/5/2
 * Time: 10:43
 */
class ShopManageController extends Controller
{
    public function __construct()
    {
        if($this->isAdmin()!=true){
            exit();
        }

    }

    public function add(){
        include_once(VIEW_PATH."shopManage.html");
    }
    public function edit(){
        $ShopModel = new ShopModel("miao_userinfo");
        $shopInfo = $ShopModel->getAllShopItem();
        include_once (VIEW_PATH."shopEdit.html");
    }
    public function upImg(){
        $this->helper('UpFile');
        $upFile = new UpFile();
        $img = $upFile->upImgFile($_FILES['img'],PUBLIC_PATH."upload/");
        if($img!=false){
            echo $img;
        }else {
            echo "false";
        }
    }
    public function addGoods(){
        $shopManageModel = new ShopManageModel("miao_shop");
        $name = $_POST['goodsName'];
        $type = $_POST['type'];
        $tag = $_POST['tag'];
        $core = $_POST['core'];
        $memory = $_POST['memory'];
        $month = $_POST['month']*1;
        $price = $_POST['price']*1;
        $avatar = $_POST['img'];
        $is_sale = 0;
        if(isset($_POST['switch'])){
            $is_sale = 1;
        }
        $result = $shopManageModel->addGoods($name,$tag,$core,$memory,$price,$month,$type,$avatar,$is_sale);
        if($result!=false){
            header("location: index.php?c=shopManage&a=edit");
        }
    }
    public function getGoodsInfo(){
        if(!isset($_GET['sid'])){
            exit;
        }
        $sid = $_GET['sid'];
        $shopModel = new ShopModel("miao_shop");
       $result = $shopModel->getShopInfo($sid)[0];
        if($result!=false){
            echo json_encode($result);
        }else {
            echo "false";
        }
    }
    public function modifyGoods(){
        $shopManageModel = new ShopManageModel("miao_shop");
        $shopid = $_POST['shopid'];
        $name = $_POST['goodsName'];
        $type = $_POST['type'];
        $tag = $_POST['tag'];
        $core = $_POST['core'];
        $memory = $_POST['memory'];
        $month = $_POST['month']*1;
        $price = $_POST['price']*1;
        $avatar = $_POST['img'];
        $is_sale = 0;
        if(isset($_POST['switch'])){
            $is_sale = 1;
        }
        $result = $shopManageModel->updateGoods($shopid,$name,$tag,$core,$memory,$price,$month,$type,$avatar,$is_sale);
        echo $result;
        if($result!=false){
            header("location: index.php?c=shopManage&a=edit");
        }
    }
    public function upDownGoods(){
        $shopid = $_POST['shopid'];
        $switch = $_POST['switch'];
        $shopManageModel = new ShopManageModel("miao_shop");
        $result = $shopManageModel->upDownGoods($shopid,$switch);
        if($result!==false){
            echo "true";
        }
    }
    public function deleteGoods(){
        $shopid = $_POST['shopid'];
        $shopManageModel = new ShopManageModel("miao_shop");
        $result = $shopManageModel->deleteGoods($shopid);
        if($result!=false){
            echo "true";
        }
    }
}