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

use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;

/**
 * 自定义任务模版类
 */
class Task extends AbstractAsyncTask
{
	/**
	 * [$callbackClass 任务模版回调类]
	 * @var string
	 */
	public static $callbackClass 	= '\App\common\ExtendsCallback';

	/**
	 * [addTask 添加一个任务模版]
	 * @Author    como
	 * @DateTime  2019-08-09
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $object   [description]
	 * @param     [type]     $callback [description]
	 * @param     array      $params   [description]
	 * @param     boolean    $static   [description]
	 */
	public static function addTask($callback = null,$params = [],$className = null)
	{
		if(empty($className)) $className = self::$callbackClass;
		$taskClass = new self(['class'=>$className,'callback'=>$callback,'params'=>$params]);
		return TaskManager::async($taskClass);
	}
	/**
	 * [run 实现任务投递模版方法]
	 * @Author    como
	 * @DateTime  2019-08-09
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $taskData     [description]
	 * @param     [type]     $taskId       [description]
	 * @param     [type]     $fromWorkerId [description]
	 * @param     [type]     $flags        [description]
	 * @return    [type]                   [description]
	 */
	protected function run($taskData,$taskId,$fromWorkerId,$flags = null)
	{
		$result = self::appResult('任务执行失败,请遵守框架定义的任务模版进行任务配置或使用原生的任务投递方式');
		if(is_array($taskData) && !empty($taskData['class']) && !empty($taskData['callback'])){
			if(class_exists($taskData['class'])) {
				$obj = new \ReflectionClass($taskData['class']);
				if($obj->hasMethod($taskData['callback'])) {
					$result = call_user_func([$taskData['class'],$taskData['callback']],$taskData['params']);
				} else {
					$result = self::appResult($taskData['class'].' method '.$taskData['callback'].' is not found');
				}
			} else {
				$result = self::appResult($taskData['class'].' is not found');
			}
		}
		return $result;
	}


	/**
	 * [finish 任务完成回调]
	 * @Author    como
	 * @DateTime  2019-08-09
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $result  [description]
	 * @param     [type]     $task_id [description]
	 * @return    [type]              [description]
	 */
	protected function finish($result,$task_id)
	{}
	/**
	 * [appResult 统一返回值]
	 * @author 	   szjcomo
	 * @createTime 2019-10-26
	 * @param      string     $info  [description]
	 * @param      [type]     $data  [description]
	 * @param      boolean    $err   [description]
	 * @param      integer    $error [description]
	 * @return     [type]            [description]
	 */
	protected static function appResult(string $info,$data = null,$err = true,$error = 0)
	{
		return ['info'=>$info,'data'=>$data,'err'=>$err,'error'=>$error];
	}

}