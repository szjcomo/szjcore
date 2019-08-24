<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷管理系统 1.5.0
 * @Author : como 
 * 版权申明：szjshop网上管理系统不是一个自由软件，是思智捷科技官方推出的商业源码，严禁在未经许可的情况下
 * 拷贝、复制、传播、使用szjshop网店管理系统的任意代码，如有违反，请立即删除，否则您将面临承担相应
 * 法律责任的风险。如果需要取得官方授权，请联系官方http://www.sizhijie.com
 * |-----------------------------------------------------------------------------------
 */

namespace szjcomo\szjcore;
use EasySwoole\Http\AbstractInterface\Controller as easyController;
use EasySwoole\Http\Message\Status;
use szjcomo\szjcore\Router;
use szjcomo\szjcore\Context;
use EasySwoole\EasySwoole\Config;
// session相关
use EasySwoole\Session\FileSessionHandler;
use EasySwoole\Session\SessionDriver;

/**
 * szjphp基类控制器
 */
////////////////////////////////////////////////////////////////////
//                          _ooOoo_                               //
//                         o8888888o                              //
//                         88" . "88                              //
//                         (| ^_^ |)                              //
//                         O\  =  /O                              //
//                      ____/`---'\____                           //
//                    .'  \\|     |//  `.                         //
//                   /  \\|||  :  |||//  \                        //
//                  /  _||||| -:- |||||-  \                       //
//                  |   | \\\  -  /// |   |                       //
//                  | \_|  ''\---/''  |   |                       //
//                  \  .-\__  `-`  ___/-. /                       //
//                ___`. .'  /--.--\  `. . ___                     //
//            \  \ `-.   \_ __\ /__ _/   .-` /  /                 //
//      ========`-.____`-.___\_____/___.-`____.-'========         //
//                           `=---='                              //
//      ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^        //
//         佛祖保佑       永无BUG       永不修改                     //
////////////////////////////////////////////////////////////////////
Class Controller extends easyController{

	/**
	 * 请求环境的上下文
	 */
	Protected $context;
    /**
     * session
     */
    Protected $_session;
	/**
	 * [index 实现默认index方法]
	 * @Author    como
	 * @DateTime  2019-08-09
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	Public function index(){}

    /**
     * [session session]
     * @Author    como
     * @DateTime  2019-08-20
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     [type]     $key [description]
     * @param     string     $val [description]
     * @return    [type]          [description]
     */
    Public function session($key = null,$val = ''){
        $this->sessionHandler(Config::getInstance()->get('SESSION'));
        if(!empty($key) && is_null($val)){
            return $this->_session->unset($key);
        } else if(!empty($key) && !empty($val)){
            return $this->_session->set($key,$val);
        } else if(!empty($key) && empty($val)){
            return $this->_session->get($key);
        }
        if(is_null($key) && is_null($val)){
            return $this->_session->destroy();
        }
        return false;
    }

    /**
     * [_FileSessionHandler 文件类型session驱动调用]
     * @Author    como
     * @DateTime  2019-08-20
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     array      $config [description]
     * @return    [type]             [description]
     */
    Protected function _FileSessionHandler($config = []){
        $handler = new FileSessionHandler();
        $this->_session = new SessionDriver($handler,$this->request(),$this->response());
        $this->_session->savePath($config['path']);
        $this->_session->sessionName($config['prefix']);
        if(!empty($config['auto_start'])){
            $this->_session->start();
        }
    }
    /**
     * [session 初始化session]
     * @Author    como
     * @DateTime  2019-08-20
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     array      $config [description]
     * @return    [type]             [description]
     */
    Private function sessionHandler($conf = []){
        $defaultConfig = ['driver'=>'File','path'=>'./session','auto_start'=>true,'prefix' =>'szjkj'];
        if($this->_session == null && is_array($conf)){
            $config = array_merge($defaultConfig,$conf);
            switch ($config['driver']) {
                case 'File':
                    $this->_FileSessionHandler($config);
                    break;
            }
        }
    }

	/**
	 * [appResult 全局统一使用的默认返回值]
	 * @Author    como
	 * @DateTime  2019-08-09
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $info [description]
	 * @param     [type]     $data [description]
	 * @param     boolean    $err  [description]
	 * @return    [type]           [description]
	 */
	Public function appResult($info = '',$data = null,$err = true){
		return ['info'=>$info,'data'=>$data,'err'=>$err];
	}
	
	

	/**
	 * [appJson 返回json数据]
	 * @Author    como
	 * @DateTime  2019-08-09
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     array      $data [description]
	 * @param     integer    $code [description]
	 * @return    [type]           [description]
	 */
	Public function appJson($data = [],$code = 200){
		return $this->writeJson($code,$data);
	}
	/**
	 * [writeJson 重写json响应数据]
	 * @Author    como
	 * @DateTime  2019-08-09
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     integer    $statusCode [description]
	 * @param     [type]     $result     [description]
	 * @param     [type]     $msg        [description]
	 * @return    [type]                 [description]
	 */
    Protected function writeJson($statusCode = 200, $result = null, $msg = null){
        if (!$this->response()->isEndResponse()) {
        	try{
	            $this->response()->write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        	} catch(\Exception $err){
        		$data = $this->appResult($err->getMessage(),$result);
        		$this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        		$statusCode = 500;
        	}
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }
    /**
     * [initialize 控制器执行前置动作]
     * @Author    como
     * @DateTime  2019-08-09
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @return    [type]     [description]
     */
    Public function initialize(){
    	/*注意：如果为false 则不继续往下执行了 可用作权限处理 token认证等功能*/
    	return true;
    }
    /**
     * [onRequest 前置拉截装置]
     * @Author    como
     * @DateTime  2019-08-09
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @return    [type]     [description]
     */
    Public function onRequest(?string $action): ?bool{
    	$this->context = new Context($this->request(),$this->response());
    	return $this->initialize();
    }
    /**
     * [onException 重写异常功能]
     * @Author    como
     * @DateTime  2019-08-09
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     \Throwable $throwable [description]
     * @return    [type]                [description]
     */
	Protected function onException(\Throwable $err): void{
       	$this->appJson($this->appResult($err->getMessage()));
    }
    /**
     * [actionNotFound 空操作]
     * @Author    como
     * @DateTime  2019-08-12
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     string     $action [description]
     * @return    [type]             [description]
     */
    Protected function actionNotFound(?string $action): void{
        $this->_empty($action);
        return;
    }
    /**
     * [_empty 空操作设置]
     * @Author    como
     * @DateTime  2019-08-12
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @return    [type]     [description]
     */
    Public function _empty($action){
    	return $this->appJson($this->appResult($action.' method action is not found'));
    }
    /**
     * [gc 垃圾回收机制]
     * @Author    como
     * @DateTime  2019-08-20
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @return    [type]     [description]
     */
    Protected function gc(){
        if($this->context){
            $this->context = null;
        }
        if(!empty($this->_session)){
            $this->_session->close();
            $this->_session = null;            
        }
        parent::gc();
    }
}