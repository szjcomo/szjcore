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

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
/**
 * 自定义路由功能
 */
class Routers extends AbstractRouter{
	/**
	 * [initialize 实现路由初始化功能]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     RouteCollector $routeCollector [description]
	 * @return    [type]                         [description]
	 */
	function initialize(RouteCollector $routeCollector){
		try{
			call_user_func([&$this,'_registerRouter'],$routeCollector);
		} catch(\Exception $err){
			print_r($err);
		}
	}
	/**
	 * [_registerRouter 注册自定义路由功能]
	 * @Author    como
	 * @DateTime  2019-08-12
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $route [description]
	 * @return    [type]            [description]
	 */
	Protected static function _registerRouter($route){}

}