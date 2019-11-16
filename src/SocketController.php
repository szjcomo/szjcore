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

use EasySwoole\Socket\AbstractInterface\Controller as EasySwooleSocketController;
use EasySwoole\EasySwoole\ServerManager 		   as EasySwooleServerManager;
use szjcomo\szjcore\Tasks 						   as AppTasks;
use szjcomo\szjcore\Request 					   as AppReqeust;
use EasySwoole\EasySwoole\Core 					   as EasySwooleCore;
use EasySwoole\EasySwoole\Logger 				   as EasySwooleLogger;
use EasySwoole\EasySwoole\Config 				   as EasySwooleConfig;

/**
 * socket 基类控制器
 */
class SocketController extends EasySwooleSocketController
{

	/**
	 * [param 获取参数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @return     [type]     [description]
	 */
	public function param(string $name = '',$default = null,$hander = null)
	{
		$data = $this->caller()->getArgs();
		return AppReqeust::input($data,$name,$default,$hander);
	}

	/**
	 * [appJson 响应JSON数据]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      [type]     $data [description]
	 * @return     [type]           [description]
	 */
	public function appJson($data)
	{
		return $this->response()->setMessage(json_encode($data));
	}
	/**
	 * [appResult 全局统一返回函数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      string       $message [description]
	 * @param      [type]       $data    [description]
	 * @param      bool|boolean $err     [description]
	 * @param      int|integer  $code    [description]
	 * @return     [type]                [description]
	 */
	public function appResult(string $message,$data = null,bool $err = true,int $code = 0)
	{
		return ['message'=>$message,'data'=>$data,'err'=>$err,'code'=>$code];
	}

    /*
     * 返回false的时候为拦截
     */
    protected function onRequest(?string $actionName):bool
    {
        $bool = call_user_func([$this,'initialize']);
        if($bool === true) return true;
        return false;
    }
    /**
     * [initialize 重新定义初始化函数]
     * @author 	   szjcomo
     * @createTime 2019-11-15
     * @return     [type]     [description]
     */
    public function initialize()
    {
    	return true;
    }
	/**
	 * [getip 获取当前客户]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @return     [type]     [description]
	 */
	public function getCurClient()
	{
		return $this->caller()->getClient()->getFd();
	}
	/**
	 * [push 同步主动给客户推送消息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      [type]     $message [description]
	 * @return     [type]              [description]
	 */
	public function push($message,$client = null,$type = 'websocket')
	{
		$client = empty($client)?$this->getCurClient():$client;
		$server = EasySwooleServerManager::getInstance()->getSwooleServer();
		if($type === 'websocket') return $server->push($client,$message);
		return $server->send($client,$this->tcp_encode($message));
	}
	/**
	 * [async_push 异步主动给客户端推送消息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      [type]     $message [description]
	 * @return     [type]              [description]
	 */
	public function async_push($message,$client = null,$type = 'websocket')
	{
		$client = empty($client)?$this->getCurClient():$client;
		return AppTasks::async(function() use ($client,$message,$type){
			$server = EasySwooleServerManager::getInstance()->getSwooleServer();
			if($type == 'websocket') $server->push($client,$message);
			$server->send($client,$this->tcp_encode($message));
		});
	}

	/**
	 * [encode tcp消息处理]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @return     [type]     [description]
	 */
	public function tcp_encode($message = null)
	{
		$tmp = $message;
		if(is_array($message)){
			$tmp = json_encode($message);
		}
		return pack('N', strlen($tmp)) . $tmp;
	}

	/*
	 * [actionNotFound 方法不存在时调用]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      string     $actionName [description]
	 * @return     [type]                 [description]
	 */
    protected function actionNotFound(?string $actionName)
    {
    	return call_user_func([$this,'_empty'],$actionName);
    }
    /**
     * [_empty description]
     * @author 	   szjcomo
     * @createTime 2019-11-15
     * @return     [type]     [description]
     */
    public function _empty(?string $actionName)
    {
    	return $this->appJson($this->appResult($actionName.' is not found'));
    }
    /**
     * [onException 控制器出现异常时调用]
     * @author 	   szjcomo
     * @createTime 2019-11-15
     * @param      \Throwable $throwable [description]
     * @return     [type]                [description]
     */
    protected function onException(\Throwable $throwable):void
    {
    	$isDev = EasySwooleCore::getInstance()->isDev();
    	if($isDev) throw $throwable;
    	$message = $throwable->getFile() . ' position ' . $throwable->getLine() . ' line ,ERROR_INFO: ' . $throwable->getMessage();
    	EasySwooleLogger::getInstance()->error($message);
    	$produceErrConfig = EasySwooleConfig::getInstance()->getConf('APP_PRODUCE_ERROR_MESSAGE');
    	if($produceErrConfig === false) $this->appJson($this->appResult($message,null,true,$throwable->getCode()));
    	$this->appJson($this->appResult($produceErrConfig,null,true,$throwable->getCode()));
    }
}
