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
use EasySwoole\Http\Response as easyResponse;
use EasySwoole\Utility\File;
Class Context {
	/**
	 * 获取easyRequest
	 */
	Private $context;

	/**
	 * [__construct 构造函数]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     easyRequest $request [description]
	 */
	Public function __construct(easyRequest $request,easyResponse $response){
		$this->context = ['req'=>$request,'res'=>$response];
	}

	/**
	 * [isPost 判断是否post请求]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    boolean    [description]
	 */
	Public function method(){
		$action = $this->context['req']->getMethod();
		return $action;
	}
	/**
	 * [isGet 判断是否get请求]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    boolean    [description]
	 */
	Public function isGet(){
		return $this->method() == 'GET';
	}
	/**
	 * [isPost 判断是否post请求]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    boolean    [description]
	 */
	Public function isPost(){
		return $this->method() == 'POST';
	}
	/**
	 * [isAjax 判断是否ajax请求]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    boolean    [description]
	 */
	Public function isAjax(){
		return false;
	}
	/**
	 * [isPut 是否put请求]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    boolean    [description]
	 */
	Public function isPut(){
		return $this->method() == 'PUT';
	}
	/**
	 * [isDelete 是否delete请求]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    boolean    [description]
	 */
	Public function isDelete(){
		return $this->method() == 'DELETE';
	}

	/**
	 * [put 获取put请求]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $name    [description]
	 * @param     [type]     $default [description]
	 * @param     string     $filter  [description]
	 * @return    [type]              [description]
	 */
	Public function put(){
		return Request::put(null,null,null,$this->context['req']);
	}
	/**
	 * [uploads 获取用户上传的文件信息]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $name    [description]
	 * @param     [type]     $options [description]
	 * @return    [type]              [description]
	 */
	Public function uploads($name = null,$options = []){
		$data = Request::uploads($name,$this->context['req']);
		if(empty($data)) return $this->appResult('没有文件被上传');
		$result = [];
		if(is_array($data)){
			foreach($data as $key=>$val){
				$res = $this->uploadsHandler($val,$options,$key);
				$tempName = $val->getTempName();
				@unlink($tempName);
				$result[$key] = $res;
			}
		} else {
			$res = $this->uploadsHandler($data,$options,$name);
			if($res['err'] == true){
				$tempName = $val->getTempName();
				@unlink($tempName);
			}
			$result = $res;
		}
		return $result;
	}

	/**
	 * [appResult 返回统一格式]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $info [description]
	 * @param     [type]     $data [description]
	 * @param     boolean    $err  [description]
	 * @return    [type]           [description]
	 */
	Public function appResult($info = '',$data = null,$err = true){
		return ['info'=>$info,'data'=>$data,'err'=>$err];
	}
	/**
	 * [uploadsHandler 文上传处理类]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $uploadFile [description]
	 * @return    [type]                 [description]
	 */
	Public function uploadsHandler($uploadFile,$options = [],$fileName = ''){
		$defaultOptions = [
			'limitSize'=>2*1024*1024,'savePath'=>'./','saveName'=>'','ext'=>['jpg','png','jpeg','gif','bmp'],
			'fileType'=>['image/jpeg','image/png','image/gif','image/bmp','image/jpg'],
		];
		$map = array_merge($defaultOptions,$options);
		try{
			if(is_object($uploadFile)){
				$streamtmp = $uploadFile->getStream();
				$fileSize = $uploadFile->getSize();
				if($fileSize > $map['limitSize']){
					return $this->appResult('上传的文件大小超过了'.$map['limitSize'].'个字节的限制');
				}
				$ext = $this->getFileNameExt($uploadFile->getClientFilename());
				if(!in_array($ext, $map['ext'])){
					return $this->appResult('上传的文件后缀不合法,请检查');
				}
				$fileType = $uploadFile->getClientMediaType();
				if(!in_array($fileType,$map['fileType'])){
					return $this->appResult('上传的文件类型不合法,请检查');
				}
				$file_exists_true = false;
				if(!file_exists($map['savePath'])){
					$file_exists_true = File::createDirectory($map['savePath']);
				} else {
					$file_exists_true = true;
				}
				if($file_exists_true !== true){
					return $this->appResult('指定的文件保存路径无法创建成功,请进行权限检查');
				}
				if(empty($map['saveName'])){
					$map['saveName'] = date('YmdHis').mt_rand(100000,999999).'.'.$ext;
				} else {
					if(!stripos($map['saveName'],'.')){
						$map['saveName'] .= '.'.$ext;
					}
				}
				$action = $uploadFile->moveTo($map['savePath'].$map['saveName']);
				if($action){
					$tmp = ['savePath'=>$map['savePath'],'saveName'=>$map['saveName'],'size'=>$fileSize,'type'=>$fileType,'ext'=>$ext,'inputName'=>$fileName];
					return $this->appResult('SUCCESS',$tmp,false);
				} else {
					return $this->appResult('文件移动失败,请进行权限检查');
				}
			} else {
				return $this->appResult('不是一个文件对象,无法操作文件指针');
			}			
		} catch(\Throwable $err){
			return $this->appResult($err->getMessage());
		}
	}
	/**
	 * [getFileNameExt 获取文件后缀名]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $fileName [description]
	 * @return    [type]               [description]
	 */
	Public function getFileNameExt($fileName = ''){
		$data = pathinfo($fileName);
		if(!empty($data) && !empty($data['extension'])){
			return $data['extension'];
		}
		return '';
	}


	/**
	 * [get 获取get参数]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $name    [description]
	 * @param     [type]     $default [description]
	 * @param     string     $filter  [description]
	 * @return    [type]              [description]
	 */
	Public function get($name = '', $default = null, $filter = ''){
		return Request::get($name,$default,$filter,$this->context['req']);
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
	Public function post($name = '',$default = null,$filter = ''){
		return Request::post($name,$default,$filter,$this->context['req']);
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
	Public function param($name = '',$default = null,$filter = ''){
		return Request::param($name,$default,$filter,$this->context['req']);
	}
	/**
	 * [getip 获取用户的Ip]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	Public function getip(){
		return Request::getip($this->context['req']);
	}
	/**
	 * [cookie 获取或设置cookie]
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
	 * @return    [type]               [description]
	 */
	Public function cookie($name = null, $value = '', $expire = null, $path = '/', $domain = '', $secure = false, $httponly = false){
		if(!empty($name) && is_null($value)){
			if(is_null($expire)) $expire = time() - 3600;
			return Response::setCookie($name,$value,$expire,$path,$domain,$secure,$httponly,$this->context['res']);
		}
		if(empty($value)) {
			$data = $this->context['req']->getCookieParams($name);
			return $this->cookieHandler($data);
		}
		if(empty($name)) return false;
		return Response::setCookie($name,$value,$expire,$path,$domain,$secure,$httponly,$this->context['res']);
	}
	/**
	 * [cookieHandler 处理cookie数据类型]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $data [description]
	 * @return    [type]           [description]
	 */
	Protected function cookieHandler($data){
		if(empty($data)) return $data;
		if(is_array($data)){
			$result = [];
			$callback = function($value,$key) use(&$result){
				if(strpos($value, '___szjtype')){
					$result[$key] = json_decode($value,true);
				}
			};
			array_walk($data);
			if(isset($result['___szjtype'])) unset($result['___szjtype']);
			return $result;
		} else {
			if(stripos($data, '___szjtype')){
				$tmp = json_decode($data,true);
				if(isset($tmp['___szjtype'])) unset($tmp['___szjtype']);
				return $tmp;
			} else {
				return $data;
			}
		}
	}
	/**
	 * [header 设置/获取请求头信息]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $name  [description]
	 * @param     [type]     $value [description]
	 * @return    [type]            [description]
	 */
	Public function header($name = null,$value = null){
		if(empty($value)) {
			if(empty($name)) return $this->context['req']->getHeaders();
			return $this->context['req']->getHeader($name);
		}
		return Response::setHeader($name,$value,$this->context['res']);
	}
	/**
	 * [redirect 重定向]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     [type]     $url  [description]
	 * @param     integer    $code [description]
	 * @return    [type]           [description]
	 */
	Public function redirect($url,$code = 302){
		return Response::redirect($url,$code,$this->context['res']);
	}
	/**
	 * [_servers 获取环境变量$_SERVER信息]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	Public function _servers(){
		return $this->context['req']->getServerParams();
	}
}