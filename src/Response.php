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

use EasySwoole\Http\Response as easyResponse;
use EasySwoole\Http\Message\Status;

/**
 * 响应请求
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
		$response->withHeader('server','szjkj-server');
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
		if(is_array($value)) {
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