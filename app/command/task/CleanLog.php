<?php

namespace app\command\Task;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class CleanLog extends Command
{
    protected function configure()
    {
        $this->setName('task:clean-log')
             ->setDescription('清理7天前的日志文件');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
	        // 写你的业务逻辑
	        $path = runtime_path('log');
	        $files = glob($path . '/*.log');
	        $now = time();
	        $expire = 7 * 86400;

	        foreach ($files as $file) {
	            if (is_file($file) && ($now - filemtime($file)) > $expire) {
	                unlink($file);
	                $output->writeln("已删除: " . basename($file));
	            }
	        }
	        Log::info('定时任务执行成功', ['time' => date('Y-m-d H:i:s')]);
	        $output->writeln('日志清理完成1');
	        return 0;
	    } catch (\Exception $e) {
	        Log::error('定时任务执行失败', ['error' => $e->getMessage()]);
	        $output->writeln('Error: ' . $e->getMessage());
	        return 1;
	    }
    }
}