<?php
namespace Yangyao\Crontab;
use Yangyao\Crontab\Parser;
//TODO dependent on shopex queue 
class Schedule{
    private $hander;
    public function __construct(AbstractHandler $hander){
        $this->hander = $hander;
    }
    public function trigger_one($cron_id){
        if ($this->hander->get($cron_id)){
            $now = time();
            if (($now - $cron['last'])<60) {
                throw new \Exception('1分钟之内不能重复执行');
            }
            //add_task
            $worker = $cron['id'];
            system_queue::instance()->publish('crontab:'.$worker, $worker);
            self::__log($cron_id, $now, 'add queue ok');
            $this->hander->update($cron_id,$now);
        }
    }

    public function trigger_all(){
        $cronentries =$this->hander->all();
        ignore_user_abort(1);
        $now = time();
        $filter = array();
        foreach ($cronentries as $cron) {
            if ($now >= Parser::parse($cron['schedule'], $cron['last'])) {
                //todo: base_queue::instance()->addTask()
                //todo: update 变更为一次性更新
                $worker = $cron['id'];
                system_queue::instance()->publish('crontab:'.$worker, $worker);
                $filter['id'][] = $cron['id'];
                self::__log($cron['id'], $now, 'add queue ok');
            }
        }
        if (!empty($filter)) {
            $this->hander->update($filter['id'],$now);
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
        return $this->hander->get($cron_id);
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
        //set_error_handler(array(self, 'error_handler'));
        $add_time = time();
        $class_name = $cron_id;
        $class = new $class_name();
        if ($class instanceof base_interface_task) {
            self::__log($cron_id, time(), 'run start');
            $class->exec();
            self::__log($cron_id, time(), 'run over');
            return true;
        }else{
            self::__log($cron_id, time(), 'run fail: cannot find the call class');
            return false;
        }
    }

    static private function __log($cron_id, $add_time, $msg){
        logger::info(sprintf("crontab task:%s add_time:%s | %s",
                             $cron_id,
                             //date("F j, Y, g:i a", $add_time),
                             date('Y-m-d H:m:i', $add_time),
                             $msg));
    }
}
