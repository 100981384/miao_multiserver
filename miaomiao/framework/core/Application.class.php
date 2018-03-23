<?php
class Application
{
    public static function run()
    {
        self::init();
        self::autoload();
        self::dispatch();
    }
    public static function init()
    {
        //目录斜线
        define("DS",DIRECTORY_SEPARATOR);
        //根目录
        define("ROOT",getcwd() . DS);
        //安装目录
        define("SETUP_PATH",dirname(ROOT) . DS);
        //kod目录
        define("KOD_COOKIE","KOD_SESSION_ID_7872c");
        //Mc版本Jar目录
        define("MC_SERVER_JAR",ROOT.DS."McServerJar");
        //Mc版本初始文件目录
        define("MC_SERVER_DEFAULT",ROOT.DS."McServerDefault");
        //主程序目录
        define("APP_PATH",ROOT . "application" . DS);
        //框架目录
        define("FRAMEWORK_PATH",ROOT . "framework" . DS);
        //公用文件目录
        define("PUBLIC_PATH",ROOT . "public" . DS);
        //配置文件目录
        define("CONFIG_PATH",APP_PATH ."config" . DS);
        //控制器目录
        define("CONTROLLER_PATH",APP_PATH ."controller" . DS);
        //模块目录
        define("MODEL_PATH",APP_PATH ."model" . DS);
        //视图目录
        define("VIEW_PATH",APP_PATH ."view" . DS);
        //框架核心目录
        define("CORE_PATH",FRAMEWORK_PATH ."core" . DS);
        //数据库目录
        define("DATABASE_PATH",FRAMEWORK_PATH ."database" . DS);
        //辅助组件目录
        define("HELPER_PATH",FRAMEWORK_PATH ."helper" . DS);
        //库目录
        define("LIBRARY_PATH",FRAMEWORK_PATH . "library" . DS);
        //上传目录
        define("UPLOAD_PATH",PUBLIC_PATH . "upload" . DS);
        //定义页面
        define("CONTROLLER",isset($_GET['c']) ? ucfirst($_GET['c']):"Index");
        //定义操作
        define("Action",isset($_GET['a'])?$_GET['a']:"Index");
        //定义公共目录
        define("CUR_PUBLIC","public".DS);
        //引入控制器
        include "Controller.class.php";
        include "Model.class.php";
        include DATABASE_PATH . "Mysql.class.php";
        $GLOBALS['config'] = include CONFIG_PATH ."config.php";
    }
    public static function dispatch(){
        $controller_name = CONTROLLER . "Controller";
        $action_name = Action;
        $controller = new $controller_name;
        $controller->$action_name();
    }
    public static function autoload(){
        spl_autoload_register('self::loader');
    }
    public static function loader($classname){
        //控制器
        if(substr($classname,-10)=="Controller") {
            if(file_exists(CONTROLLER_PATH ."{$classname}.class.php")){
                // echo 233;
                include CONTROLLER_PATH . "{$classname}.class.php";
                // echo 233;
            }
            // echo CONTROLLER_PATH.$classname;
        }
        //模型
        else if (substr($classname,-5=="Model")){
            if(file_exists(MODEL_PATH. "{$classname}.class.php")){
                include MODEL_PATH ."{$classname}.class.php";
            }
        }
        else {
            
        }
    }
}