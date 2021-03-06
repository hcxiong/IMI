<?php
namespace Imi\Task\Listener;

use Imi\Task\TaskInfo;
use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\TaskEventParam;
use Imi\Server\Event\Listener\ITaskEventListener;

/**
 * @Listener("IMI.MAIN_SERVER.TASK")
 */
class MainServer implements ITaskEventListener
{
	/**
	 * 事件处理方法
	 * @param TaskEventParam $e
	 * @return void
	 */
	public function handle(TaskEventParam $e)
	{
		RequestContext::create();
		try{
			$taskInfo = $e->data;
			if($taskInfo instanceof TaskInfo)
			{
				call_user_func([$taskInfo->getTaskHandler(), 'handle'], $taskInfo->getParam(), $e->server, $e->taskID, $e->workerID);
			}
		}
		catch(\Throwable $ex)
		{
			throw $ex;
		}
		finally{
			// 释放请求的进程池资源
			PoolManager::destroyCurrentContext();
			RequestContext::destroy();
		}
	}
}