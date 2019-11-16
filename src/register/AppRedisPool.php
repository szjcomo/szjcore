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

use EasySwoole\Pool\Config;
use EasySwoole\Pool\AbstractPool;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Redis\Config\RedisClusterConfig;
use EasySwoole\Redis\Redis;

/**
 * redis 连接池的实现
 */
class AppRedisPool extends AbstractPool
{
	/**
	 * [$redisConfig redis配置项]
	 * @var [type]
	 */
    protected $redisConfig;

    /**
     * [__construct 重写构造函数,为了传入redis配置]
     * @author 	   szjcomo
     * @createTime 2019-11-05
     * @param Config      $conf
     * @param RedisConfig $redisConfig
     * @throws \EasySwoole\Pool\Exception\Exception
     */
    public function __construct(Config $config,RedisConfig $redisConfig)
    {
        parent::__construct($config);
        $this->redisConfig = $redisConfig;
    }

    /**
     * [createObject 创建一个对象]
     * @author 	   szjcomo
     * @createTime 2019-11-05
     * @return     EasySwoole\Redis\Redis     [description]
     */
    protected function createObject()
    {
        $redis = null;
        if ($this->redisConfig instanceof RedisClusterConfig){
            $redis = new RedisCluster($this->redisConfig);
        }else{
            $redis = new Redis($this->redisConfig);
        }
        return $redis;
    }
}