<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷信息科技有限公司
 * @Author : szjcomo 
 * |-----------------------------------------------------------------------------------
 */

namespace szjcomo\szjcore\register;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\EasySwoole\Swoole\EventRegister  as EasySwooleEventRegister;

/**
 * 总注册器
 */
class Register
{
	/**
	 * [before_register 前置注册]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @return     [type]     [description]
	 */
	public static function before_register()
	{
		AppConfig::register();
	}
	/**
	 * [initialize_register 生命周期函数初始化注册器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @return     [type]     [description]
	 */
	public static function initialize_register($before_register = null)
	{
		try{
			if(empty($before_register)) self::before_register();
			if(is_callable($before_register)) call_user_func($before_register);
			$config = Config::getInstance()->getConf('APP_INITIALIZE_REGISTER');
			self::registerHandler($config);		
		} catch(\Throwable $err){
			throw $err;
		}
	}
	/**
	 * [server_create_register 服务创建时注册器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @return     [type]     [description]
	 */
	public static function server_create_register(callable $before_register = null,EasySwooleEventRegister $register = null)
	{
		try{
			if(is_callable($before_register)) call_user_func($before_register);
			$config = Config::getInstance()->getConf('APP_SERVER_CREATE_REGISTER');
			self::registerHandler($config,$register);	
		} catch(\Throwable $err){
			throw $err;
		}
	}
	/**
	 * [http_on_request_register 有http请求时注册器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @param      Request    $request  [description]
	 * @param      Response   $response [description]
	 * @return     [type]               [description]
	 */
	public static function http_on_request_register(Request $request,Response $response)
	{
		try{
			$config = Config::getInstance()->getConf('APP_ONREQUEST_REGISTER');
			self::httpRegisterHandler($config,$request,$response);
		} catch(\Throwable $err){
			throw $err;
		}
	}
	/**
	 * [registerHandler 注册管理器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @param      array      $config [description]
	 * @return     [type]             [description]
	 */
	protected static function registerHandler(array $config,EasySwooleEventRegister $register = null)
	{
		if(is_array($config)) {
			foreach($config as $key=>$val) {
				$className = '';
				$bool = false;
				if(!is_array($val) && class_exists($val)){
					$bool = call_user_func($val.'::register');				
				} else {
					list($className,$params) = $val;
					if($params === 'register'){
						$params = $register;
					}
					if(class_exists($className)) $bool = call_user_func($className.'::register',$params);
				}
				if($bool === false) throw new Exception($className.' fail register'.PHP_EOL);
			}
		}
	}
	/**
	 * [httpRegisterHandler 注册全局http拦截器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @param      array      $config   [description]
	 * @param      Request    $request  [description]
	 * @param      Response   $response [description]
	 * @return     [type]               [description]
	 */
	protected static function httpRegisterHandler(array $config,Request $request,Response $response)
	{
		if(is_array($config)){
			foreach($config as $key=>$val){
				$className = '';
				$bool = false;
				if(is_array($val)){
					switch(count($val)){
						case 2:
							list($className,$params) = $val;
							switch($params){
								case 'request':
									$params = $request;
									break;
								case 'response':
									$params = $response;
									break;
							}
							if(class_exists($className)) $bool = call_user_func($className.'::register',$params);
							break;
						case 3:
							list($className,$params1,$params2) = $val;
							switch($params1){
								case 'request':
									$params1 = $request;
									break;
								case 'response':
									$params1 = $response;
									break;
							}
							switch($params2){
								case 'request':
									$params2 = $request;
									break;
								case 'response':
									$params2 = $response;
									break;
							}
							if(class_exists($className)) $bool = call_user_func($className.'::register',$params1,$params2);
							break;
						default:
							$className = $val[0];
							$bool = call_user_func($className.'::register');
					}
				}
				if($bool === false) throw new Exception($className.' fail register'.PHP_EOL);
			}
		}
	}
}