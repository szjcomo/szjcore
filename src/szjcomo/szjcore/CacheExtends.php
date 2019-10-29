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

use EasySwoole\FastCache\Cache as EasySwooleCache;
use EasySwoole\FastCache\CacheProcessConfig;
use EasySwoole\FastCache\SyncData;
use EasySwoole\Utility\File;

/**
 * fastcache 类增加文件存档功能
 */
class CacheExtends 
{
	/**
	 * 设置配置项
	 */
	protected static  $CONFIG = [
		//数据写入频率 默认为每隔5秒检查写入一次
		'write_time_rate'=>5,
		'write_path'=>''
	];

	/**
	 * [run 缓存类增加存档功能]
	 * @author 	   szjcomo
	 * @createTime 2019-10-29
	 * @return     [type]     [description]
	 */
	public static function run(array $config = [])
	{
		$conf = array_merge(self::$CONFIG,$config);
		//设置检查频率
		self::setWriteRate($conf['write_time_rate']);
		//开启定时检查写入数据
		self::writeCacheData($conf['write_path']);
		//服务器停止时写入数据
		self::stopWriteCacheData($conf['write_path']);
		//服务器启动时需要加载数据
		self::loadCacheData($conf['write_path']);
	}
	/**
	 * [loadCacheData 启动时加载缓存]
	 * @author 	   szjcomo
	 * @createTime 2019-10-29
	 * @param      string     $cachePath [description]
	 * @return     [type]                [description]
	 */
	protected static function loadCacheData(string $cachePath = '')
	{
		if(empty($cachePath)) $cachePath = EASYSWOOLE_TEMP_DIR . '/FastCacheData/';
        // 启动时将存回的文件重新写入
        EasySwooleCache::getInstance()->setOnStart(function (CacheProcessConfig $cacheProcessConfig) use ($cachePath) {
            $path = $cachePath. $cacheProcessConfig->getProcessName();
            if(is_file($path)){
                $data = unserialize(file_get_contents($path));
                $syncData = new SyncData();
                $syncData->setArray($data['data']);
                $syncData->setQueueArray($data['queue']);
                $syncData->setTtlKeys(($data['ttl']));
                // queue支持
                $syncData->setJobIds($data['jobIds']);
                $syncData->setReadyJob($data['readyJob']);
                $syncData->setReserveJob($data['reserveJob']);
                $syncData->setDelayJob($data['delayJob']);
                $syncData->setBuryJob($data['buryJob']);
                return $syncData;
            }
        });
	}

	/**
	 * [setWriteRate 设置默认检查时间]
	 * @author 	   szjcomo
	 * @createTime 2019-10-29
	 * @param      int|integer $writetime [description]
	 */
	protected static function setWriteRate(int $writetime = 5)
	{
        // 每隔5秒将数据存回文件
        EasySwooleCache::getInstance()->setTickInterval($writetime * 1000);//设置定时频率
	}
	/**
	 * [stopWriteCacheData 服务器停止时写入文件]
	 * @author 	   szjcomo
	 * @createTime 2019-10-29
	 * @param      string     $writepath [description]
	 * @return     [type]                [description]
	 */
	protected static function stopWriteCacheData(string $writepath = '')
	{
		if(empty($writepath)) $writepath = EASYSWOOLE_TEMP_DIR . '/FastCacheData/';
        // 在守护进程时,php easyswoole stop 时会调用,落地数据
        EasySwooleCache::getInstance()->setOnShutdown(function (SyncData $SyncData, CacheProcessConfig $cacheProcess) use ($writepath) {
            $data = [
                'data'  => $SyncData->getArray(),
                'queue' => $SyncData->getQueueArray(),
                'ttl'   => $SyncData->getTtlKeys(),
                 // queue支持
                'jobIds'     => $SyncData->getJobIds(),
                'readyJob'   => $SyncData->getReadyJob(),
                'reserveJob' => $SyncData->getReserveJob(),
                'delayJob'   => $SyncData->getDelayJob(),
                'buryJob'    => $SyncData->getBuryJob(),
            ];
            $path = $writepath . $cacheProcess->getProcessName();
            File::createFile($path,serialize($data));
        });
	}
	/**
	 * [writeCacheData 写入缓存数据到文件]
	 * @author 	   szjcomo
	 * @createTime 2019-10-29
	 * @param      string     $path [description]
	 * @return     [type]           [description]
	 */
	protected static function writeCacheData(string $writepath = '')
	{
		if(empty($writepath)) $writepath = EASYSWOOLE_TEMP_DIR . '/FastCacheData/';
        EasySwooleCache::getInstance()->setOnTick(function (SyncData $SyncData, CacheProcessConfig $cacheProcess) use($writepath) {
            $data = [
                'data'  => $SyncData->getArray(),
                'queue' => $SyncData->getQueueArray(),
                'ttl'   => $SyncData->getTtlKeys(),
             // queue支持
                'jobIds'     => $SyncData->getJobIds(),
                'readyJob'   => $SyncData->getReadyJob(),
                'reserveJob' => $SyncData->getReserveJob(),
                'delayJob'   => $SyncData->getDelayJob(),
                'buryJob'    => $SyncData->getBuryJob(),
            ];
            $path = $writepath . $cacheProcess->getProcessName();
            File::createFile($path,serialize($data));
        });
	}

}