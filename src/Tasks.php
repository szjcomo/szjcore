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

use EasySwoole\EasySwoole\Task\TaskManager as EasySwooleTaskManager;

/**
 * 任务类
 */
class Tasks
{
	/**
	 * [async 投递一个异步任务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-14
	 * @return     [type]     [description]
	 */
	public static function async($task,callable $finishCallback = null,$taskWorkerId = null)
	{
		return EasySwooleTaskManager::getInstance()->async($task,$finishCallback,$taskWorkerId);
	}
	/**
	 * [sync 投递一个同步任务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-14
	 * @param      [type]     $task         [description]
	 * @param      float      $timeout      [description]
	 * @param      [type]     $taskWorkerId [description]
	 * @return     [type]                   [description]
	 */
	public static function sync($task,$timeout = 3.0,$taskWorkerId = null)
	{
		return EasySwooleTaskManager::getInstance()->sync($task,$timeout,$taskWorkerId);
	}
	/**
	 * [status 获取所有任务状态]
	 * @author 	   szjcomo
	 * @createTime 2019-11-14
	 * @return     [type]     [description]
	 */
	public static function status()
	{
		return EasySwooleTaskManager::getInstance()->status();
	}
	/**
	 * [addTask 添加一个任务]
	 * @author 	   szjcomo
	 * @createTime 2019-11-14
	 * @param      [type]        $task           [description]
	 * @param      callable|null $finishCallback [description]
	 * @param      [type]        $taskWorkerId   [description]
	 */
	public static function addTask($task,callable $finishCallback = null,$taskWorkerId = null)
	{	
		return self::async($task,$finishCallback,$taskWorkerId);
	}

}