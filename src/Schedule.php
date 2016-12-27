<?php
namespace Yangyao\Crontab;
use Yangyao\Crontab\Parser;
use Yangyao\Crontab\Handler\AbstractHandler;
use Yangyao\Queue\Queue;
use Yangyao\Queue\Contracts\Task;
class Schedule{
    private $handler;
    private $queue;
    public function __construct(AbstractHandler $handler,Queue $queue){
        $this->handler = $handler;
        $this->queue = $queue;
    }
    public function trigger_one($cron_id){
        if ($cron = $this->handler->get($cron_id)){
            $now = time();
            if (($now - $cron['last'])<60) {
                throw new \Exception('1分钟之内不能重复执行');
            }
            //add_task
            $worker = $cron['id'];
            $this->queue->publish('crontab:'.$worker, $worker);
            $this->handler->update($cron_id,$now);
        }
    }

    public function trigger_all(){
        $cronentries =$this->handler->all();
        ignore_user_abort(1);
        $now = time();
        $filter = array();
        foreach ($cronentries as $cron) {
            if ($now >= Parser::parse($cron['schedule'], $cron['last'])) {
                $worker = $cron['id'];
                $this->queue->publish('crontab:'.$worker, $worker);
                $filter['id'][] = $cron['id'];
            }
        }
        if (!empty($filter)) {
            $this->handler->update($filter['id'],$now);
        }
    }


    /**
     * 检查是否为有效任务
     *
     * 只做单次处理. 如果是批量执行请勿用
     *
     * @param int $cron_id 任务ID
     * @return bool
     */
    public function is_valid_cronentry($cron_id){
        return $this->handler->get($cron_id);
    }


    /**
     * 执行crontab任务
     *
     * 正常执行任务, 流程通过system_queue::run_task进行处理
     * 此函数目前只为测试执行单次任务服务, cmd & crontab后台执行命令
     *
     * @param int $cron_id 任务ID
     * @return bool
     */
    static public function run_task($cron_id){
        $class_name = $cron_id;
        $class = new $class_name();
        if ($class instanceof Task) {
            $class->exec();
            return true;
        }
        return false;
    }
}
