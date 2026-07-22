<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Log;
use app\common\service\FeedService;

class AutoPublish extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('autopublish')
            ->setDescription('文章自动发布');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        try {
            $feedService = new FeedService();
            Log::info('定时文章发布任务执行中...', ['time' => date('Y-m-d H:i:s')]);
            $feedService->createWeb(31);
            Log::info('定时文章发布任务执行成功', ['time' => date('Y-m-d H:i:s')]);
            $output->writeln('文章自动发布完成');
            return 0;
        } catch (\Exception $e) {
            Log::error('定时文章发布任务执行失败', ['error' => $e->getMessage()]);
            $output->writeln('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
