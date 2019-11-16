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
use EasySwoole\Pool\Config as PoolConfig;
use EasySwoole\Pool\Manager;
use EasySwoole\Redis\Config\RedisConfig;

/**
 * redis服务注册
 */
class AppRedis
{
	/**
	 * [register 注册redis服务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-12
	 * @return     [type]     [description]
	 */
	public static function register()
	{
		$config = Config::getInstance()->getConf('REDIS');
		if($config['register_redis'] === true){
			$pool_arr = ['maxIdleTime'=>$config['maxIdleTime'],'maxObjectNum'=>$config['maxObjectNum'],'minObjectNum'=>$config['minObjectNum']];
			$pool_config = new PoolConfig($pool_arr);
			foreach($config['connecConfigs'] as $key=>$val){
				Manager::getInstance()->register(new AppRedisPool($pool_config,new RedisConfig($val)),'redis_'.$key);
			}
		}
		return true;
	}
}