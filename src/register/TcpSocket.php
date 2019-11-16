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

use EasySwoole\EasySwoole\ServerManager 		as EasySwooleServerManager;
use EasySwoole\EasySwoole\Config 				as EasySwooleConfig;

/**
 * tcpsocket 注册器
 */
class TcpSocket
{
	/**
	 * [register 注册器的实现]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @return     [type]     [description]
	 */
	public static function register()
	{
        $isOpen     = EasySwooleConfig::getInstance()->getConf('APP_TCPSOCKET_OPEN');
        if($isOpen === true){
            $tcpPort    = EasySwooleConfig::getInstance()->getConf('APP_TCPSOCKET_PORT');
            $tcpHost    = EasySwooleConfig::getInstance()->getConf('MAIN_SERVER.LISTEN_ADDRESS');
            $server     = EasySwooleServerManager::getInstance()->getSwooleServer();
            $subPort    = $server->addListener($tcpHost, $tcpPort, SWOOLE_TCP);
            $socketConfig       = new \EasySwoole\Socket\Config();
            $socketConfig->setType($socketConfig::TCP);
            $tcpSocketParser    = EasySwooleConfig::getInstance()->getConf('APP_TCPSOCKET_MESSAGE_PARSER');
            $socketConfig->setParser(new $tcpSocketParser);
            //设置解析异常时的回调,默认将抛出异常到服务器
            $socketConfig->setOnExceptionHandler(function ($server, $throwable, $raw, $client, $response) {
                $server->close($client->getFd());
            });
            $dispatch = new \EasySwoole\Socket\Dispatcher($socketConfig);
            $subPort->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
                echo '123456'.PHP_EOL;
                $dispatch->dispatch($server, $data, $fd, $reactor_id);
            });
            $subPort->set(['open_length_check'=> true,'package_max_length'=> 2097152,'package_length_type'=> 'N','package_length_offset' => 0,'package_body_offset'=> 4]);
            //注册链接时回调事件
            $onConnection = EasySwooleConfig::getInstance()->getConf('APP_TCPSOCKET_ON_CONNECT');
            $onClose      = EasySwooleConfig::getInstance()->getConf('APP_TCPSOCKET_ON_CLOSE');
            $subPort->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) use ($onConnection){
                if($onConnection !== false && class_exists($onConnection)) {
                    $class = new \ReflectionClass($onConnection);
                    $obj = $class->newInstance();
                    if(method_exists($obj, 'connect')) call_user_func([$obj,'connect'],$server,$fd,$reactor_id);
                }
            });
            $subPort->on('close', function (\swoole_server $server, int $fd, int $reactor_id) use ($onClose) {
                if($onClose !== false && class_exists($onClose)) {
                    $class = new \ReflectionClass($onClose);
                    $obj = $class->newInstance();
                    if(method_exists($obj, 'close')) call_user_func([$obj,'close'],$server,$fd,$reactor_id);
                }
            });            
        }
	}
}