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

use EasySwoole\Http\Request as EasySwooleRequest;
use EasySwoole\Http\Response as EasySwooleResponse;
use EasySwoole\Utility\File;

/**
 * 请求上下文
 */
class Context 
{
	/**
	 * 获取easyRequest
	 */
	private $context;

	/**
	 * [__construct 构造函数]
	 * @Author    como
	 * @DateTime  2019-08-13
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     easyRequest $request [description]
	 */
	public function __construct(EasySwooleRequest $request,EasySwooleResponse $response)
	{
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
	public function method()
	{
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
	public function isGet()
	{
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
	public function isPost()
	{
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
	public function isAjax()
	{
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
	public function isPut()
	{
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
	public function isDelete()
	{
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
	public function put()
	{
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
	public function uploads($name = null,$options = [])
	{
		$data = Request::uploads($name,$this->context['req']);
		if(empty($data)) return $this->appResult('没有文件被上传');
		$result = [];
		if(is_array($data)) {
			foreach($data as $key=>$val){
				$res = $this->uploadsHandler($val,$options,$key);
				$tempName = $val->getTempName();
				@unlink($tempName);
				$result[$key] = $res;
			}
		} else {
			$res = $this->uploadsHandler($data,$options,$name);
			if($res['err'] == true){
				$tempName = $data->getTempName();
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
	public function appResult($info = '',$data = null,$err = true,int $code = 0)
	{
		return ['info'=>$info,'data'=>$data,'err'=>$err,'code'=>$code];
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
	public function uploadsHandler($uploadFile,$options = [],$fileName = '')
	{
		$defaultOptions = [
			'limitSize'=>2*1024*1024,'savePath'=>'./','saveName'=>'','ext'=>['jpg','png','jpeg','gif','bmp']
		];
		$map = array_merge($defaultOptions,$options);
		try{
			if(is_object($uploadFile)) {
				$streamtmp = $uploadFile->getStream();
				$fileSize = $uploadFile->getSize();
				if($fileSize > $map['limitSize']) {
					return $this->appResult('上传的文件大小超过了'.$map['limitSize'].'个字节的限制');
				}
				$ext = $this->getFileNameExt($uploadFile->getClientFilename());
				if(!in_array($ext, $map['ext'])) {
					return $this->appResult('上传的文件类型不合法,请检查');
				}
				$file_exists_true = false;
				if(!file_exists($map['savePath'])) {
					$file_exists_true = File::createDirectory($map['savePath']);
				} else {
					$file_exists_true = true;
				}
				if($file_exists_true !== true) {
					return $this->appResult('指定的文件保存路径无法创建成功,请进行权限检查');
				}
				if(empty($map['saveName'])) {
					$map['saveName'] = date('YmdHis').mt_rand(100000,999999).'.'.$ext;
				} else {
					if(!stripos($map['saveName'],'.')){
						$map['saveName'] .= '.'.$ext;
					}
				}
				$action = $uploadFile->moveTo($map['savePath'].$map['saveName']);
				if($action) {
					$tmp = ['savePath'=>$map['savePath'],'saveName'=>$map['saveName'],'size'=>$fileSize,'type'=>$uploadFile->getClientMediaType(),'ext'=>$ext,'inputName'=>$fileName];
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
	public function getFileNameExt($fileName = '')
	{
		$data = pathinfo($fileName);
		if(!empty($data) && !empty($data['extension'])) {
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
	public function get($name = '', $default = null, $filter = '')
	{
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
	public function post($name = '',$default = null,$filter = '')
	{
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
	public function param($name = '',$default = null,$filter = '')
	{
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
	public function getip()
	{
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
	public function cookie($name = null, $value = '', $expire = null, $path = '/', $domain = '', $secure = false, $httponly = false)
	{
		if(!empty($name) && is_null($value)) {
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
	protected function cookieHandler($data)
	{
		if(empty($data)) return $data;
		if(is_array($data)){
			$result = [];
			$callback = function($value,$key) use(&$result){
				if(strpos($value, '___szjtype')) {
					$result[$key] = json_decode($value,true);
				}
			};
			array_walk($data);
			if(isset($result['___szjtype'])) unset($result['___szjtype']);
			return $result;
		} else {
			if(stripos($data, '___szjtype')) {
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
	public function header($name = null,$value = null)
	{
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
	public function redirect($url,$code = 302)
	{
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
	public function _servers()
	{
		return $this->context['req']->getServerParams();
	}
}