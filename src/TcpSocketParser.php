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

use EasySwoole\Socket\Bean\Caller 							as EasySwooleCaller;
use EasySwoole\Socket\Bean\Response 						as EasySwooleSocketResponse;			
use EasySwoole\Socket\AbstractInterface\ParserInterface 	as EasySwooleScoketParserInterface;
use EasySwoole\EasySwoole\Config 							as EasySwooleConfig;

/**
 * tcp服务器解析器
 */
class TcpSocketParser implements EasySwooleScoketParserInterface
{
	/**
	 * [decode tcp内容解析器]
	 * @author 	   szjcomo
	 * @createTime 2019-11-15
	 * @param      [type]     $raw    [description]
	 * @param      [type]     $client [description]
	 * @return     [type]             [description]
	 */
    public function decode($raw, $client): ?EasySwooleCaller
    {
        $data = substr($raw, '4');
        //为了方便,我们将json字符串作为协议标准
        $data 		= json_decode($data, true);
        if (!is_array($data)) return null;
        $bean 		= new EasySwooleCaller();  
        $param 		= !empty($data['content']) ? $data['content'] : [];
        $namespace 	= EasySwooleConfig::getInstance()->getConf('APP_TCPSOCKET_NAMESPACE');
        $class 		= $namespace . ucfirst($data['class'] ?? 'Index');
        $bean->setControllerClass($class);
        // 设置被调用的方法
        $bean->setAction($data['action'] ?? 'index');
        $bean->setArgs($param);
        return $bean;
    }
    /**
     * [encode 只处理pack,json交给控制器]
     * @author 	   szjcomo
     * @createTime 2019-11-15
     * @param Response $response
     * @param          $client
     * @return string|null
     */
    public function encode(EasySwooleSocketResponse $response, $client): ?string
    {
        return pack('N', strlen($response->getMessage())) . $response->getMessage();
    }
}