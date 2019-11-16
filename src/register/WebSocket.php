<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷信息科技有限公司
 * @Author : szjcomo 
 * |-----------------------------------------------------------------------------------
 */

namespace szjcomo\szjcore\register;

use EasySwoole\Socket\Dispatcher 				as EasySwooleSocketDispatcher;
use EasySwoole\EasySwoole\Config 				as EasySwooleConfig;
use EasySwoole\EasySwoole\Swoole\EventRegister  as EasySwooleEventRegister;

/**
 * websocket 注册
 */
class WebSocket
{
	/**
	 * [register websocket 注册功能]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @return     [type]     [description]
	 */
	public static function register($register = null)
	{
		$isOpen = EasySwooleConfig::getInstance()->getConf('MAIN_SERVER.SERVER_TYPE');
		if($isOpen == EASYSWOOLE_WEB_SOCKET_SERVER){
		    // 创建一个 Dispatcher 配置
		    $conf = new \EasySwoole\Socket\Config();
		    // 设置 Dispatcher 为 WebSocket 模式
		    $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);
		   	// 获取消息解析器
		    $websockerParser = EasySwooleConfig::getInstance()->getConf('APP_WEBSOCKET_MESSAGE_PARSER');
		    // 设置解析器对象
		    $conf->setParser(new $websockerParser);
		    // 创建 Dispatcher 对象 并注入 config 对象
		    $dispatch = new EasySwooleSocketDispatcher($conf);
		    // 给server 注册相关事件 在 WebSocket 模式下  on message 事件必须注册 并且交给 Dispatcher 对象处理
		    $register->set(EasySwooleEventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
		        $dispatch->dispatch($server, $frame->data, $frame);
		    });
            //注册链接时回调事件
            $onConnection = EasySwooleConfig::getInstance()->getConf('APP_WEBSOCKET_ON_CONNECT');
            $onClose      = EasySwooleConfig::getInstance()->getConf('APP_WEBSOCKET_ON_CLOSE');
		    // 给server 注册连接事件
		    $register->set(EasySwooleEventRegister::onOpen,function(\swoole_websocket_server $server, \swoole_http_request $request) use ($onConnection){
                if($onConnection !== false && class_exists($onConnection)) {
                    $class = new \ReflectionClass($onConnection);
                    $obj = $class->newInstance();
                    if(method_exists($obj, 'connect')) call_user_func([$obj,'connect'],$server,$request);
                }
		    });
		    // 给server 注册断开事件
		    $register->set(EasySwooleEventRegister::onClose,function(\swoole_websocket_server $server, $fd) use($onClose) {
                if($onClose !== false && class_exists($onClose)) {
                    $class = new \ReflectionClass($onClose);
                    $obj = $class->newInstance();
                    if(method_exists($obj, 'close')) call_user_func([$obj,'close'],$server,$fd);
                }
		    });
		}
	}

}