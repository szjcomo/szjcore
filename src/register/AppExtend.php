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

use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\SysConst;

/**
 * 其它扩展类型注册
 */
class AppExtend
{
	/**
	 * [register 注册其它类型服务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	public static function register()
	{
		self::namespaceRegister();
		self::httpContrMaxNum();
		return true;
	}
	/**
	 * [namespaceRegister 重新定义命名空间服务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	protected static function namespaceRegister()
	{
		$namespace = Config::getInstance()->getConf('APP_HTTP_NAMESPACE');
		Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE,$namespace);
	}
	/**
	 * [httpControllerMaxNum 注册链接数最大值]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	protected static function httpContrMaxNum()
	{
		$maxNumber = Config::getInstance()->getConf('HTTP_CONTROLLER_POOL_MAX_NUM');
		Di::getInstance()->set(SysConst::HTTP_CONTROLLER_POOL_MAX_NUM,$maxNumber);
	}

}