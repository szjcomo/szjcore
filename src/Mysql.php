<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷信息科技有限公司
 * @Author : szjcomo 
 * |-----------------------------------------------------------------------------------
 */

namespace szjcomo\szjcore;

use EasySwoole\Pool\Manager 			as EasySwooleManager;

/**
 * mysql类供外界调用
 */
class Mysql
{
	/**
	 * [DB 默认调用的数据库]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      string     $name [description]
	 */
	public static function DB(string $name = null)
	{
		if(empty($name)) $name = 'mysql_default';
		$pool = EasySwooleManager::getInstance()->get($name);
		return $pool->defer();
	}
	/**
	 * [name 设置表名称]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      string     $name [description]
	 * @return     [type]           [description]
	 */
	public static function name(string $name,$poolName = null)
	{
		return self::DB($poolName)->name($name);
	}
	/**
	 * [table 主要用于子查询用 其它情况小心使用这个函数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      string     $name [description]
	 * @return     [type]           [description]
	 */
	public static function table(string $name,$poolName = null)
	{
		return self::DB($poolName)->table($name);
	}
}