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
 * redis操作类
 */
class Redis
{

	/**
	 * [DB 获取操作类对象]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      string     $name [description]
	 */
	public static function DB(string $name = null)
	{	
		if(empty($name)) $name = 'redis_default';
		$pool = EasySwooleManager::getInstance()->get($name);
		return $pool->defer();
	}

	/*以下为快捷方式调用,因为redis命令太多 如果想要更多的快捷方式 请自行实现吧*/

	/**
	 * [get 获取一个key]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      string     $key [description]
	 * @return     [type]          [description]
	 */
	public static function get(string $key,string $poolName = null)
	{
		$value = self::DB($poolName)->get($key);
        $value_serl = @unserialize($value);
        if(is_object($value_serl) || is_array($value_serl)){
            return $value_serl;
        }
		return $value_serl;
	}
	/**
	 * [set 设置数据]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $key     [description]
	 * @param      [type]     $val     [description]
	 * @param      integer    $timeout [description]
	 */
	public static function set($key, $value, $timeout = 0,$poolName = null)
	{
		if(is_object($value)||is_array($value)){
            $value = serialize($value);
        }
		return self::DB($poolName)->set($key,$value,$timeout);
	}
	/**
	 * [del 删除一个key]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $key [description]
	 * @return     [type]          [description]
	 */
	public static function del($key,$poolName = null)
	{
		return self::DB($poolName)->del($key);
	}
	/**
	 * [keys 获取key集合]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $pattern [description]
	 * @return     [type]              [description]
	 */
	public static function keys($pattern,$poolName = null)
	{
		return self::DB($poolName)->del($pattern);
	}
	/**
	 * [exists 判断一个key是否存在]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $key      [description]
	 * @param      [type]     $poolName [description]
	 * @return     [type]               [description]
	 */
	public static function exists($key,$poolName = null)
	{
		return self::DB($poolName)->exists($key);
	}
	/**
	 * [expire 设置一个key的过期时间]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $key        [description]
	 * @param      integer    $expireTime [description]
	 * @param      [type]     $poolName   [description]
	 * @return     [type]                 [description]
	 */
	public static function expire($key,$expireTime = 60,$poolName = null)
	{
		return self::DB($poolName)->expire($key,$expireTime);
	}
	/**
	 * [ttl 不想写了 请看官方注释吧]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $key [description]
	 * @return     [type]          [description]
	 */
	public static function ttl($key,$poolName = null)
	{
		return self::DB($poolName)->ttl($key);
	}
	/**
	 * [rename description]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $key     [description]
	 * @param      [type]     $new_key [description]
	 * @return     [type]              [description]
	 */
	public static function rename($key,$new_key,$poolName = null)
	{
		return self::DB($poolName)->rename($key,$new_key);
	}
	/**
	 * [type description]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $key      [description]
	 * @param      [type]     $poolName [description]
	 * @return     [type]               [description]
	 */
	public static function type($key,$poolName = null)
	{
		return self::DB($poolName)->type($key);
	}

}