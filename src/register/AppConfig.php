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

use EasySwoole\EasySwoole\Config 		as EasySwooleConfig;
use EasySwoole\Utility\File 			as EasySwooleFile;

/**
 * 项目配置注册服务
 */
class AppConfig
{
	/**
	 * [register 项目配置注册]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	public static function register(string $configPath = '')
	{
		if(empty($configPath)) $configPath = EASYSWOOLE_ROOT . '/config';
        //加载自定义配置文件
        self::loadAppConfig($configPath);
        return true;
	}
	/**
	 * [loadAppConfig 加载项目下的自定义配置]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	protected static function loadAppConfig(string $configPath)
	{
        $config  = EasySwooleConfig::getInstance();
        $datas 	 = EasySwooleFile::scanDirectory($configPath);
        if (empty($datas) || empty($datas['files']) || !is_array($datas['files']))  return;
        foreach ($datas['files'] as $file) $config->loadFile($file,true);
	}
}