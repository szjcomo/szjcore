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

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\Request;
use EasySwoole\EasySwoole\Config;

/**
 * ip限流处理
 */
class AppIplimit
{
	/**
	 * [$app_iplimit_open 是否开启IP限流服务]
	 * @var boolean
	 */
	protected static $app_iplimit_open = null;
	/**
	 * [$app_iplimit_max_number 每5秒请求次数限制]
	 * @var integer
	 */
	protected static $app_iplimit_max_number = null;

	/**
	 * [register 注册IP]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	public static function register(bool $listen = false,Request $request = null)
	{
		if(is_null(self::$app_iplimit_open)) self::$app_iplimit_open = Config::getInstance()->getConf('APP_IPLIMIT_OPEN');
		if(self::$app_iplimit_open){
			if(is_null(self::$app_iplimit_max_number)) self::$app_iplimit_max_number = Config::getInstance()->getConf('APP_IPLIMIT_MAX_NUMBER');
			if($listen === true) {
				self::ipLimitProcess();
			} else {
				return self::ipLimitIntercept($request);
			}
		}
		return true;
	}

	/**
	 * [ipLimitProcess IP限流是否开启定时检测服务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @param      bool|boolean $start [description]
	 * @return     [type]              [description]
	 */
	public static function ipLimitProcess()
	{
        // 开启IP限流
        AppIplimitProcess::getInstance();
        $class = new class('IpAccessCount') extends AbstractProcess{
            protected function run($arg){
                $this->addTick(5*1000, function (){
                    AppIplimitProcess::getInstance()->clear();
                });
            }
        };
        ServerManager::getInstance()->getSwooleServer()->addProcess(($class)->getProcess());
	}
	/**
	 * [ipLimitIntercept 创建拦截器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	public static function ipLimitIntercept(Request $request = null)
	{
		if(self::$app_iplimit_open && !empty($request) && !is_null(self::$app_iplimit_max_number)){
			$fd 		= $request->getSwooleRequest()->fd;
			$ip 		= ServerManager::getInstance()->getSwooleServer()->getClientInfo($fd)['remote_ip'];
			//$maxNumber 	= Config::getInstance()->getConf('APP_IPLIMIT_MAX_NUMBER');
			$maxNumber  = self::$app_iplimit_max_number; 
			if (AppIplimitProcess::getInstance()->access($ip) < $maxNumber) return true;
			ServerManager::getInstance()->getSwooleServer()->close($fd);
			return false;			
		}
		return true;
	}
}