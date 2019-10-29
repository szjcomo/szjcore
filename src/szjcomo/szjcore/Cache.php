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

use EasySwoole\FastCache\Cache as easyCache;

/**
 * 缓存类
 */
class Cache 
{
	/**
	 * [get 读取轻量级缓存]
	 * @Author    como
	 * @DateTime  2019-08-10
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $key     [description]
	 * @param     float      $timeout [description]
	 * @return    [type]              [description]
	 */
	public static function get($key = null,$timeout = 1.0)
	{
		if(empty($key)) {
			$key = self::keys($timeout);
		}
		$result = null;
		if(is_array($key)) {
			$result = self::getAll($key,$timeout);
		} else {
			$result = easyCache::getInstance()->get($key,$timeout);
		}
		return empty($result)?null:$result;
	}
	/**
	 * [set 设置轻量级缓存数据]
	 * @Author    como
	 * @DateTime  2019-08-10
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $key     [description]
	 * @param     [type]     $value   [description]
	 * @param     integer    $expire  [以秒作为单位时间]
	 * @param     float      $timeout [description]
	 */
	public static function set($key,$value = null,$expire = null,$timeout = 1.0)
	{
		$setCount = 0;
		if(empty($key)) return $setCount;
		if(!empty($value)) {
			if(is_array($key) && (count($key) == count($value))){
				$setCount = self::setAll($key,$value,$expire,$timeout);
			} else if(is_string($key)) {
				easyCache::getInstance()->set($key,$value,$expire,$timeout);
				$setCount = 1;
			} else {}		
		} else {
			self::del($key,$timeout);
			$setCount = 1;
		}
		return $setCount;
	}
	/**
	 * [setAll 批量设置缓存]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     array      $keys    [description]
	 * @param     array      $value   [description]
	 * @param     integer    $expire  [description]
	 * @param     float      $timeout [description]
	 */
	public static function setAll($keys = [],$value = [],$expire = 0,$timeout = 1.0)
	{
		$setCount = 0;
		if(count($keys) == count($value)){
			foreach ($keys as $key => $val) {
				$setCount++;
				easyCache::getInstance()->set($val,$value[$key],$expire,$timeout);
			}
		}
		return $setCount;
	}

	/**
	 * [del 删除一个key值]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $key     [description]
	 * @param     float      $timeout [description]
	 * @return    [type]              [description]
	 */
	public static function del($key,$timeout = 1.0)
	{
		if(empty($key)) return null;
		return easyCache::getInstance()->unset($key,$timeout);
	}
	/**
	 * [clear 清空所有缓存]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     float      $timeout [description]
	 * @return    [type]              [description]
	 */
	public static function clear($bool = false,$timeout = 1.0)
	{
		$clearCount = 0;
		if($bool === true){
			$keys = easyCache::getInstance()->keys();
			if(!empty($keys) && is_array($keys)) {
				foreach ($keys as $key => $value) {
					self::del($value,$timeout);
					$clearCount++;
				}
			}
		}
		return $clearCount;
	}
	/**
	 * [getAll 获取所有的缓存信息]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     array      $keys [description]
	 * @return    [type]           [description]
	 */
	public static function getAll($keys = [],$timeout = 1.0)
	{
		$result = [];
		foreach($keys as $key=>$val){
			$result[] = easyCache::getInstance()->get($val);
		}
		return $result;
	}
	/**
	 * [keys 获取所有的keys]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     float      $timeout [description]
	 * @return    [type]              [description]
	 */
	public static function keys($timeout = 1.0)
	{
		$arr = easyCache::getInstance()->keys();
		return empty($arr)?[]:$arr;
	}
	/**
	 * [count 获取缓存总数]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     float      $timeout [description]
	 * @return    [type]              [description]
	 */
	public static function count($timeout = 1.0)
	{
		$keys = self::keys();
		return count($keys);
	}
	/**
	 * [getExpire 获取一个key的过期时间]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $key     [description]
	 * @param     float      $timeout [description]
	 * @return    [type]              [description]
	 */
	public static function getExpire($key = null,$timeout = 1.0)
	{
		if(empty($key)) return null;
		return easyCache::getInstance()->ttl($key,$timeout);
	}

}