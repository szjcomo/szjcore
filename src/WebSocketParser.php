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

use EasySwoole\Socket\AbstractInterface\ParserInterface as EasySwooleParserInterface;
use EasySwoole\Socket\Bean\Caller 						as EasySwooleCaller;
use EasySwoole\Socket\Bean\Response 					as EasySwooleResponse;
use EasySwoole\EasySwoole\Config  						as EasySwooleConfig;
use EasySwoole\EasySwoole\ServerManager 		   		as EasySwooleServerManager;

/**
 * websokcet解析器
 */
class WebSocketParser implements EasySwooleParserInterface
{
	/**
	 * [decode 消息解密器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      [type]     $raw    [description]
	 * @param      [type]     $client [description]
	 * @return     [type]             [description]
	 */
	public function decode($raw, $client) : ? EasySwooleCaller
	{
		$app_websoket_namespace = EasySwooleConfig::getInstance()->getConf('APP_WEBSOCKET_NAMESPACE');
		// 解析 客户端原始消息
        $data = json_decode($raw, true);
        if (!is_array($data)){
        	$server = EasySwooleServerManager::getInstance()->getSwooleServer();
        	$server->push($client->getFd(),json_encode(['message'=>'Message format error, please contact administrator','err'=>true,'data'=>null,'code'=>0]));
        	return null;
        }
		// new 调用者对象
        $caller =  new EasySwooleCaller();
        $class = $app_websoket_namespace . ucfirst($data['class'] ?? 'Index');
        $caller->setControllerClass($class);
        // 设置被调用的方法
        $caller->setAction($data['action'] ?? 'index');
		// 检查是否存在args
		$args = null;
        if (!empty($data['content']))  $args = is_array($data['content']) ? $data['content'] : ['content' => $data['content']];
		// 设置被调用的Args
        $caller->setArgs($args ?? []);
        return $caller;
	}
	/**
	 * [encode 消息加密器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      Response   $response [description]
	 * @param      [type]     $client   [description]
	 * @return     [type]               [description]
	 */
	public function encode(EasySwooleResponse $response, $client) : ? string
	{
		return $response->getMessage();
	}
}