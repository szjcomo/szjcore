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
/**
 * 使用原生的request
 */
use EasySwoole\Http\Request as easyRequest;
use EasySwoole\EasySwoole\ServerManager;
/**
 * 继承自request
 */
Class Request{
    /**
     * 全局过滤规则
     * @var array
     */
    Protected static $filter;

	/**
	 * [get 获取get请求参数]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	Public static function get($name = '', $default = null, $filter = '',easyRequest $request){
		if(empty($name)) return $request->getQueryParams();
		$data = $request->getQueryParams();
		if(empty($data)) return $default;
		return self::input($data,$name,$default,$filter);
	}
	/**
	 * [post 获取post请求参数]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $name    [description]
	 * @param     [type]     $default [description]
	 * @param     string     $filter  [description]
	 * @return    [type]              [description]
	 */
	Public static function post($name = '', $default = null, $filter = '',easyRequest $request){
		if(empty($name)) return $request->getParsedBody();
		$data = $request->getParsedBody();
		if(empty($data)) return $default;
		return self::input($data,$name,$default,$filter);
	}
	/**
	 * [param 获取get/post请求参数]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $name    [description]
	 * @param     [type]     $default [description]
	 * @param     string     $filter  [description]
	 * @return    [type]              [description]
	 */
	Public static function param($name = '', $default = null, $filter = '',easyRequest $request){
		if(empty($name)) return $request->getRequestParam();
		$data = $request->getRequestParam();
		if(empty($data)) return $default;
		return self::input($data,$name,$default,$filter);
	}
	/**
	 * [getip 获取客户端远程ip]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	Public static function getip(easyRequest $request){
        $ips = $request->getHeader('x-real-ip');
        if(empty($ips)){
            $fd = $request->getSwooleRequest()->fd;
            $ip = ServerManager::getInstance()->getSwooleServer()->getClientInfo($fd)['remote_ip'];
        } else {
			$ip = $ips[0];
		}
		return $ip;
	}
    /**
     * [put 获取put数据]
     * @Author    como
     * @DateTime  2019-08-14
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     string      $name    [description]
     * @param     [type]      $default [description]
     * @param     string      $filter  [description]
     * @param     easyRequest $request [description]
     * @return    [type]               [description]
     */
    Public static function put($name = '', $default = null, $filter = '',easyRequest $request){
        $data = $request->getBody()->__toString();
        return $data;
    }

    /**
     * [uploads 用户文件上传功能]
     * @Author    como
     * @DateTime  2019-08-14
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     [type]      $name    [description]
     * @param     easyRequest $request [description]
     * @return    [type]               [description]
     */
    Public static function uploads($name = null,easyRequest $request){
        $uploadFile = null;
        if(empty($name)) {
            $uploadFile = $request->getUploadedFiles();
        } else {
            $uploadFile = $request->getUploadedFile($name);
        }
        return $uploadFile;
    }

    /**
     * 获取变量 支持过滤和默认值
     * @access public
     * @param  array         $data 数据源
     * @param  string|false  $name 字段名
     * @param  mixed         $default 默认值
     * @param  string|array  $filter 过滤函数
     * @return mixed
     */
    Public static function input($data = [], $name = '', $default = null, $filter = ''){
        if (false === $name) {
            // 获取原始数据
            return $data;
        }
        $name = (string) $name;
        if ('' != $name) {
            $data = self::getData($data, $name);
            if (is_null($data)) {
                return $default;
            }
            if (is_object($data)) {
                return $data;
            }
        }
        // 解析过滤器
        $filter = self::getFilter($filter, $default);
        if (is_array($data)) {
            array_walk_recursive($data, [self, 'filterValue'], $filter);
        } else {
            self::filterValue($data, $name, $filter);
        }
        return $data;
    }
    /**
     * [getData 获取数据]
     * @Author    como
     * @DateTime  2019-08-13
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     array      $data [description]
     * @param     [type]     $name [description]
     * @return    [mixed]           [description]
     */
    Protected static function getData(array $data, $name){
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return;
            }
        }
        return $data;
    }
    /**
     * [getFilter 获取数据过滤规则]
     * @Author    como
     * @DateTime  2019-08-13
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     * @param     [type]     $filter  [description]
     * @param     [type]     $default [description]
     * @return    [type]              [description]
     */
    Protected static function getFilter($filter, $default){
    	if(empty(self::$filter)){
    		self::$filter = Config::get('default_filter');
    	}
        if (is_null($filter)) {
            $filter = [];
        } else {
            $filter = $filter ?: self::$filter;
            if (is_string($filter) && false === strpos($filter, '/')) {
                $filter = explode(',', $filter);
            } else {
                $filter = (array) $filter;
            }
        }
        $filter[] = $default;
        return $filter;
    }
    /**
     * 递归过滤给定的值
     * @access public
     * @param  mixed     $value 键值
     * @param  mixed     $key 键名
     * @param  array     $filters 过滤方法+默认值
     * @return mixed
     */
    Private static function filterValue(&$value, $key, $filters){
        $default = array_pop($filters);
        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                // 调用函数或者方法过滤
                $value = call_user_func($filter, $value);
            }
        }
        return $value;
    }
    /**
     * [__destruct 析构函数]
     * @Author    como
     * @DateTime  2019-08-13
     * @copyright 思智捷管理系统
     * @version   [1.5.0]
     */
    Public function __destruct(){
    	self::$filter = null;
    }
}