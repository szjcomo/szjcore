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

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\File;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use Swoole\Process;
use Swoole\Table;
use Swoole\Timer;

/**
 * 服务热重载
 */
class AppHotload extends AbstractProcess
{

    protected $table;
    protected $isReady = false;
    protected $monitorDir; 	// 需要监控的目录
    protected $monitorExt; 	// 需要监控的后缀

	/**
	 * [register 服务热重载注册]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	public static function register()
	{
		$isHotReloadStart 	= Config::getInstance()->getConf('APP_HOT_RELOAD_START');
		if($isHotReloadStart === true) {
			$options 		= Config::getInstance()->getConf('APP_HOT_RELOAD_CONFIG');
			ServerManager::getInstance()->getSwooleServer()->addProcess((new static('HotReload', $options))->getProcess());
		}
        return true;
	}

	/**
	 * [run 启动定时器进行循环扫描]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @param      [type]     $arg [description]
	 * @return     [type]          [description]
	 */
    public function run($arg)
    {
        // 此处指定需要监视的目录 建议只监视App目录下的文件变更
        $this->monitorDir = !empty($arg['monitorDir']) ? $arg['monitorDir'] : EASYSWOOLE_ROOT . '/app';
        // 指定需要监控的扩展名 不属于指定类型的的文件 无视变更 不重启
        $this->monitorExt = !empty($arg['monitorExt']) && is_array($arg['monitorExt']) ? $arg['monitorExt'] : ['php'];
        if (extension_loaded('inotify') && empty($arg['disableInotify'])) {
            // 扩展可用 优先使用扩展进行处理
            $this->registerInotifyEvent();
            Logger::getInstance()->info('启动服务器热重载完成 : 本次使用inotify扩展监听文件变化');
        } else {
            // 扩展不可用时 进行暴力扫描
            $this->table = new Table(512);
            $this->table->column('mtime', Table::TYPE_INT, 4);
            $this->table->create();
            Logger::getInstance()->info('启动服务器热重载完成 : 本次使用定时器监听文件变化');
            $this->runComparison();
            Timer::tick(1000, function () {
                $this->runComparison();
            });
        }
    }

    /**
     * 扫描文件变更
     */
    private function runComparison()
    {
        $startTime = microtime(true);
        $doReload = false;
        $dirIterator = new \RecursiveDirectoryIterator($this->monitorDir);
        $iterator = new \RecursiveIteratorIterator($dirIterator);
        $inodeList = array();
        // 迭代目录全部文件进行检查
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            $ext = $file->getExtension();
            if (!in_array($ext, $this->monitorExt)) {
                continue; // 只检查指定类型
            } else {
                // 由于修改文件名称 并不需要重新载入 可以基于inode进行监控
                $inode = $file->getInode();
                $mtime = $file->getMTime();
                array_push($inodeList, $inode);
                if (!$this->table->exist($inode)) {
                    // 新建文件或修改文件 变更了inode
                    $this->table->set($inode, ['mtime' => $mtime]);
                    $doReload = true;
                } else {
                    // 修改文件 但未发生inode变更
                    $oldTime = $this->table->get($inode)['mtime'];
                    if ($oldTime != $mtime) {
                        $this->table->set($inode, ['mtime' => $mtime]);
                        $doReload = true;
                    }
                }
            }
        }
        foreach ($this->table as $inode => $value) {
            // 迭代table寻找需要删除的inode
            if (!in_array(intval($inode), $inodeList)) {
                $this->table->del($inode);
                $doReload = true;
            }
        }
        if ($doReload) {
            $count = $this->table->count();
            $usage = round(microtime(true) - $startTime, 3);
            if (!$this->isReady == false) {
                // 监测到需要进行热重启
                Logger::getInstance()->info("事件监听成功 :本次耗时【 {$usage} 】 s 共计【 {$count} 】个文件");
                ServerManager::getInstance()->getSwooleServer()->reload();
            } else {
                // 首次扫描不需要进行重启操作
                Logger::getInstance()->info("热更新操作准备就绪 : 本次耗时【 {$usage} 】s , 共计【 {$count} 】个文件");
                $this->isReady = true;
            }
        }
    }
    /**
     * [registerInotifyEvent 注册Inotify监听事件]
     * @Author   szjcomo
     * @DateTime 2019-09-25
     * @return   [type]     [description]
     */
    private function registerInotifyEvent()
    {
        // 因为进程独立 且当前是自定义进程 全局变量只有该进程使用
        // 在确定不会造成污染的情况下 也可以合理使用全局变量
        global $lastReloadTime;
        global $inotifyResource;
        $lastReloadTime = 0;
        $files = File::scanDirectory($this->monitorDir);
        $files = array_merge($files['files'], $files['dirs']);
        $inotifyResource = inotify_init();
        // 为当前所有的目录和文件添加事件监听
        foreach ($files as $item) {
            inotify_add_watch($inotifyResource, $item, IN_CREATE | IN_DELETE | IN_MODIFY);
        }
        // 加入事件循环
        swoole_event_add($inotifyResource, function () {
            global $lastReloadTime;
            global $inotifyResource;
            $events = inotify_read($inotifyResource);
            if ($lastReloadTime < time() && !empty($events)) { // 限制1s内不能进行重复reload
                $lastReloadTime = time();
                foreach($events as $arr){
                    if(!empty($arr['name'])){
                        $info = $arr['mask'] == 2?'文件修改':'未知事件类型';
                        // 监测到需要进行热重启
                        Logger::getInstance()->info($info."事件监听成功 :更新文件名是:".$arr['name']);                    
                    }
                }
                ServerManager::getInstance()->getSwooleServer()->reload();
            }
        });
    }

}