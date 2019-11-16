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

use EasySwoole\EasySwoole\Config 					as EasySwooleConfig;
use EasySwoole\EasySwoole\Core 						as EasySwooleCore;
use EasySwoole\EasySwoole\Logger 					as EasySwooleLogger;
use EasySwoole\Session\AbstractSessionController	as EasySwooleController;

/**
 * 基类控制器
 */
class Controller extends EasySwooleController
{
    /**
     * 请求环境的上下文
     */
    protected $context;
    /**
     * [index 实现默认index方法]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @return     [type]     [description]
     */
    public function index(){}
    /**
     * [sessionHandler 实现session接口]
     * @author 	   szjcomo
     * @createTime 2019-11-14
     * @return     [type]     [description]
     */
    protected function sessionHandler(): \SessionHandlerInterface
    {
    	$config = EasySwooleConfig::getInstance()->getConf('SESSION');
    	$hander = $config['sessionHandler'];
        return new $hander();
    }
    /**
     * [Session session处理]
     * @author 	   szjcomo
     * @createTime 2019-11-14
     */
    protected function sessions($key = null,$val = '')
    {
    	$this->before_session();
        if(!empty($key) && is_null($val)) {
            return $this->session()->unset($key);
        } else if(!empty($key) && !empty($val)) {
            return $this->session()->set($key,$val);
        } else if(!empty($key) && empty($val)) {
            return $this->session()->get($key);
        }
        if(is_null($key) && is_null($val)) {
            return $this->session()->destroy();
        }
        return false;
    }
    /**
     * [before_session session前置]
     * @author 	   szjcomo
     * @createTime 2019-11-14
     * @return     [type]     [description]
     */
    protected function before_session()
    {
    	$config = EasySwooleConfig::getInstance()->getConf('SESSION');
    	$this->session()->savePath($config['savePath']);
    	$this->session()->sessionName($config['sessionName']);
    	$this->session()->start();
    }
    /**
     * [session_id 获取sessionid]
     * @author 	   szjcomo
     * @createTime 2019-11-14
     * @return     [type]     [description]
     */
    protected function session_id()
    {
    	return $this->session()->sessionId();
    }
    /**
     * [session_name 获取sessionname]
     * @author 	   szjcomo
     * @createTime 2019-11-14
     * @return     [type]     [description]
     */
    protected function session_name()
    {
    	return $this->session()->sessionName();
    }
    /**
     * [appResult 全局统一返回值]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @param      string       $info [description]
     * @param      [type]       $data [description]
     * @param      bool|boolean $err  [description]
     * @param      int|integer  $code [description]
     * @return     [type]             [description]
     */
    public function appResult(string $info,$data = null,bool $err = true,int $code = 0)
    {
    	return ['info'=>$info,'message'=>$info,'data'=>$data,'err'=>$err,'errCode'=>$code];
    }
    /**
     * [appJson json返回值]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @param      array      $data [description]
     * @param      integer    $code [description]
     * @return     [type]           [description]
     */
    public function appJson($data = [],int $code = 200)
    {
    	return $this->writeJson($code,$data);
    }
    /**
     * 重写json响应数据
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @param      integer    $statusCode [description]
     * @param      [type]     $result     [description]
     * @param      [type]     $msg        [description]
     * @return     [type]                 [description]
     */
    protected function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if (!$this->response()->isEndResponse()) {
            try{
                $this->response()->write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            } catch(\Exception $err){
                $data = $this->appResult($err->getMessage(),$result);
                $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $statusCode = 500;
            }
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }
    /**
     * [initialize 控制器执行前置动作]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @return     [type]     [description]
     */
    public function initialize()
    {
    	return true;
    }
    /**
     * [onRequest 前置拉截装置]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @param      string     $action [description]
     * @return     [type]             [description]
     */
    public function onRequest(?string $action): ?bool
    {
        $this->context = new Context($this->request(),$this->response());
        return $this->initialize();
    }
    /**
     * [onException 重写控制器错误方法]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @param      \Throwable $throwable [description]
     * @return     [type]                [description]
     */
    protected function onException(\Throwable $throwable): void
    {
    	$isDev = EasySwooleCore::getInstance()->isDev();
    	if($isDev) throw $throwable;
    	$message = $throwable->getFile() . ' position ' . $throwable->getLine() . ' line ,ERROR_INFO: ' . $throwable->getMessage();
    	EasySwooleLogger::getInstance()->error($message);
    	$produceErrConfig = EasySwooleConfig::getInstance()->getConf('APP_PRODUCE_ERROR_MESSAGE');
    	if($produceErrConfig === false) $this->appJson($this->appResult($message,null,true,$throwable->getCode()));
    	$this->appJson($this->appResult($produceErrConfig,null,true,$throwable->getCode()));
    }
    /**
     * [actionNotFound 操作方法不存在时]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @param      string     $action [description]
     * @return     [type]             [description]
     */
    protected function actionNotFound(?string $action)
    {
        return $this->_empty($action);
    }
    /**
     * [_empty 供外界重写的空操作]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @param      [type]     $action [description]
     * @return     [type]             [description]
     */
    public function _empty($action)
    {
        return $this->appJson($this->appResult($action.' method action is not found'));
    }
    /**
     * [gc 重写垃圾回收操作]
     * @author 	   szjcomo
     * @createTime 2019-11-13
     * @return     [type]     [description]
     */
    protected function gc()
    {
        if($this->context) {
            $this->context = null;
        }
        parent::gc();
    }

}