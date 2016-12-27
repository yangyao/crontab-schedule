<?php 

namespace Yangyao\Crontab\Handler;

abstract class AbstractHandler
{
    /**
     * Get one record form crontab table by primary key
     *
     * @param int  $cron_id crontab table primary key
     *
     * @return array
     */
    public function get($cron_id){}

    /**
     * Get all record form crontab table by primary key
     *
     * @return array
     */
    public function all(){}

    /**
     * Update one record form crontab table by primary key
     *
     * @param int  $cron_id crontab table primary key
     * @param int  $timestamp last time of tasking
     *
     * @return array
     */
    public function update($cron_id , $timestamp){}
}
