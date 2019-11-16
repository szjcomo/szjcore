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

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

/**
 * 自定义路由的实现
 */
abstract class Router extends AbstractRouter
{
	/**
	 * [registerRouter 必须要实现路由注册功能]
	 * @author 	   szjcomo
	 * @createTime 2019-11-14
	 * @param      [type]     $router [description]
	 * @return     [type]             [description]
	 */
	abstract public function registerRouter($router);

	/**
	 * [initialize 初始化功能]
	 * @author 	   szjcomo
	 * @createTime 2019-11-14
	 * @param      RouteCollector $routerCollector [description]
	 * @return     [type]                          [description]
	 */
	public function initialize(RouteCollector $routerCollector)
	{
		try{
			call_user_func([&$this,'registerRouter'],$routerCollector);
		} catch(\Throwable $err){
			throw new \Exception($err->getMessage());
		}
	}
}