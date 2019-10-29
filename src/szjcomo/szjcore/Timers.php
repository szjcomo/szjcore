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

use EasySwoole\Component\Timer;

/**
 * 自定义定时器
 */
class Timers 
{
	/**
	 * [$callbackClass 定时器任务回调类]
	 * @var string
	 */
	public static $callbackClass 	= '\App\common\ExtendsCallback';
	/**
	 * [addTimer 执行多次的定时器]
	 * @Author    como
	 * @DateTime  2019-08-10
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 */
	public static function setInterval($callback = null,$microSeconds = 5000,$params = [],$callbackClass = null)
	{
		if(!empty($callback) && is_callable($callback)) {
			return Timer::getInstance()->loop($microSeconds,$callback);
		} else if(!empty($callback) && is_string($callback)) {
			if(empty($callbackClass)) $callbackClass = self::$callbackClass;
			$callFun = function() use($callbackClass,$callback,$params){
				if(class_exists($callbackClass)){
					$obj = new \ReflectionClass($callbackClass);
					if($obj->hasMethod($callback)) {
						try{
							call_user_func([$callbackClass,$callback],$params);
						} catch(\Exception $err){
							throw $err;
						}
					}
				}
			};
			return Timer::getInstance()->loop($microSeconds,$callFun);
		} else {
			throw new Exception("Unknown type, refuse to execute");
			return -1;
		}
	}
	/**
	 * [clear 清除一个定时器]
	 * @Author    como
	 * @DateTime  2019-08-10
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     integer    $timerId [description]
	 * @return    [type]              [description]
	 */
	public static function clearInterval($timerId = 0)
	{
		if($timerId < 0) return false;
		return Timer::getInstance()->clear($timerId);
	}
	/**
	 * [setTimeout 执行一次的定时器]
	 * @Author    como
	 * @DateTime  2019-08-10
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $callback      [回调函数]
	 * @param     array      $params        [传递参数,只有在使用回调类中才能传递参数]
	 * @param     integer    $microSeconds  [定时执行的时间]
	 * @param     [type]     $callbackClass [执行回调的类名]
	 */
	public static function setTimeout($callback = null,$microSeconds = 5000,$params = [],$callbackClass = null)
	{
		if(!empty($callback) && is_callable($callback)) {
			return Timer::getInstance()->after($microSeconds,$callback);
		} elseif(!empty($callback) && is_string($callback)) {
			if(empty($callbackClass)) $callbackClass = self::$callbackClass;
			$callFun = function() use($callbackClass,$callback,$params){
				if(class_exists($callbackClass)) {
					$obj = new \ReflectionClass($callbackClass);
					if($obj->hasMethod($callback)) {
						try{
							call_user_func([$callbackClass,$callback],$params);
						} catch(\Exception $err){
							throw $err;
						}
					}
				}
			};
			return Timer::getInstance()->after($microSeconds,$callFun);
		} else {
			throw new Exception("Unknown type, refuse to execute");
			return -1;
		}
	}
}