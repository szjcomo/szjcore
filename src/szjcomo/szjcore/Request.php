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

use EasySwoole\Http\Request as easyRequest;
use EasySwoole\EasySwoole\ServerManager;

/**
 * 继承自request
 */
class Request
{
	/**
	 * [get 获取get请求参数]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	public static function get($name = '', $default = null, $filter = '',easyRequest $request)
    {
        $data = $request->getQueryParams();
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
	public static function post($name = '', $default = null, $filter = '',easyRequest $request)
    {
        $data = $request->getParsedBody();
        if(empty($data)) {
            $header = $request->getHeader('content-type');
            $reg = '/\s?application\/json/';
            if(!empty($header) && is_array($header) && !empty(preg_match($reg, $header[0]))) {
                $dataString = $request->getBody()->__toString();
                $jsonData = json_decode($dataString,true);
                if(!empty($jsonData) && is_array($jsonData)) $data = $jsonData;                
            }
        }
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
	public static function param($name = '', $default = null, $filter = '',easyRequest $request)
    {
        $data = $request->getRequestParam();
        $putdata = $request->getBody()->__toString();
        if(!empty($putdata)) {
            $tmpdata = json_decode($putdata,true);
            if(!empty($tmpdata) && is_array($tmpdata)) $data = array_merge($data,$tmpdata);
        }
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
	public static function getip(easyRequest $request)
    {
        $ips = $request->getHeader('x-real-ip');
        if(empty($ips)) {
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
    public static function put($name = '', $default = null, $filter = '',easyRequest $request)
    {
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
    public static function uploads($name = null,easyRequest $request)
    {
        $uploadFile = null;
        if(empty($name)) {
            $uploadFile = $request->getUploadedFiles();
        } else {
            $uploadFile = $request->getUploadedFile($name);
        }
        return $uploadFile;
    }

    /**
     * [input 进行数据安全过滤]
     * @author        szjcomo
     * @createTime 2019-10-28
     * @param      array      $data    [description]
     * @param      string     $name    [description]
     * @param      [type]     $default [description]
     * @param      [type]     $filter  [description]
     * @return     [type]              [description]
     */
    public static function input($data = [],string $name = '',$default = null,$filter = null)
    {
        if(false === $name) return $data;
        if(empty($filter)) $filter = Config::get('default_filter');
        $tmp = [];
        $callback = function($item,$key) use($filter,&$tmp){
            $tmp[$key] = call_user_func($filter,$item);
        };
        if('' !== $name) {
            $value = self::getData($data,$name);
            if(is_null($value)) return $default;
            if(is_array($value)) {
                self::array_recursive($value,$tmp,$filter);
                return $tmp;
            }
            return call_user_func($filter,$value);
        }
        self::array_recursive($data,$tmp,$filter);
        return $tmp;
    }

    /**
     * [array_recursive description]
     * @author        szjcomo
     * @createTime 2019-10-28
     * @param      array      $arr    [description]
     * @param      array      $result [description]
     * @param      [type]     $filter [description]
     * @return     [type]             [description]
     */
    public static function array_recursive(array $arr,array &$result,$filter = '')
    {
        foreach($arr as $key=>$val){
            if(is_array($val)) {
                $result[$key] = [];
                self::array_recursive($val,$result[$key],$filter);
            } else {
                $result[$key] = call_user_func($filter,$val);
            }
        }
    }


    /**
     * [getData 获取数据]
     * @author        szjcomo
     * @createTime 2019-10-28
     * @param      array      $data [description]
     * @param      string     $name [description]
     * @return     [type]           [description]
     */
    public static function getData(array $data,string $name)
    {
        if(isset($data[$name])) return $data[$name];
        return null;
    }
}