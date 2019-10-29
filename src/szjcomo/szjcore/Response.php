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

use EasySwoole\Http\Response as easyResponse;
use EasySwoole\Http\Message\Status;

/**
 * 自定义响应类
 */
class Response 
{

	/**
	 * [header description]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	public static function setHeader($name, $value,easyResponse $response)
	{
		$response->withHeader($name,$value);
		$response->withHeader('server','szjkj');
		return true;
	}
	/**
	 * [setCookie 设置cookie]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $name     [description]
	 * @param     [type]     $value    [description]
	 * @param     [type]     $expire   [description]
	 * @param     string     $path     [description]
	 * @param     string     $domain   [description]
	 * @param     boolean    $secure   [description]
	 * @param     boolean    $httponly [description]
	 */
	public static function setCookie($name, $value = null, $expire = null, $path = '/', $domain = '', $secure = false, $httponly = false,easyResponse $response)
	{
		if(empty($name)) return false;
		if(is_null($value)) {
			return $response->setCookie($name,$value,$expire,$path,$domain,$secure,$httponly);
		}
		$strValue = '';
		if(!is_array($value) || !is_object($value)) {
			$value['___szjtype'] = 'json';
			$strValue = json_encode($value,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}
		return $response->setCookie($name,(string)$strValue,$expire,$path,$domain,$secure,$httponly);
	}
	/**
	 * [redirect 重定向跳转]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]       $url      [description]
	 * @param     [type]       $status   [description]
	 * @param     easyResponse $response [description]
	 * @return    [type]                 [description]
	 */
	public static function redirect($url,$status = Status::CODE_MOVED_TEMPORARILY,easyResponse $response)
	{
		return $response->redirect($url,$status);
	}

}