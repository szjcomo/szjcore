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

use Swoole\Timer as SwooleTimer;

/**
 * 定时器组件
 */
class Timer
{
	/**
	 * [setInterval 循环定时器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @param      mixed   		$callback     [string|array|callable]
	 * @param      int|integer 	$microSeconds [耗秒]
	 * @param      [type]      	$params       [description]
	 */
	public static function setInterval($callback,int $microSeconds = 5000,...$params)
	{
		if(is_array($callback)){
			$tmpFun = function($tid,$params) use ($callback){
				return call_user_func($callback,$tid,$params);
			};
			return SwooleTimer::tick($microSeconds,$tmpFun,$params);
		} else {
			return SwooleTimer::tick($microSeconds,$callback,$params);
		}
		return false;
	}
	/**
	 * [setTimeout 只执行一次的定义器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @param      string|array|callable     $callback     [回调函数]
	 * @param      int|integer $microSeconds [耗秒]
	 * @param      [type]      $params       [description]
	 */
	public static function setTimeout($callback,int $microSeconds = 5000,...$params)
	{
		if(is_array($callback)){
			$tmpFun = function($params) use ($callback){
				return call_user_func($callback,$params);
			};
			return SwooleTimer::after($microSeconds,$tmpFun,$params);
		} else {
			return SwooleTimer::after($microSeconds,$callback,$params);
		}
		return false;
	}
	/**
	 * [clearInterval 请除循环定时器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @param      integer    $timerId [description]
	 * @return     [type]              [description]
	 */
	public static function clearInterval(int $timerId)
	{
		return SwooleTimer::clear($timerId);
	}
	/**
	 * [clearAll 清除所有定时器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @return     [type]     [description]
	 */
	public static function clearAll()
	{
		return SwooleTimer::clearAll();
	}
	/**
	 * [timerInfo 查看定时器详情]
	 * @author 	   szjcomo
	 * @createTime 2019-11-14
	 * @param      int        $timerId [description]
	 * @return     [type]              [description]
	 */
	public static function info(int $timerId)
	{
		return SwooleTimer::info($timerId);
	}

	/**
	 * [timerList 获取所有定时器列表]
	 * @author 	   szjcomo
	 * @createTime 2019-11-13
	 * @return     [type]     [description]
	 */
	public static function list()
	{
		return SwooleTimer::list();
	}

}