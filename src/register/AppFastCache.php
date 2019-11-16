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
use EasySwoole\FastCache\Cache;
use EasySwoole\EasySwoole\ServerManager;

/**
 * 项目快速缓存
 */
class AppFastCache
{
	/**
	 * [register 注册缓存服务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	public static function register()
	{
		$isOpen = Config::getInstance()->getConf('APP_FAST_CACHE_OPEN');
		if($isOpen === true) {
			$config = Config::getInstance()->getConf('APP_FAST_CACHE_CONFIG');
			FastCacheFile::run($config);
			Cache::getInstance()->setTempDir(EASYSWOOLE_TEMP_DIR)->attachToServer(ServerManager::getInstance()->getSwooleServer());
		}
		return true;
	}
}