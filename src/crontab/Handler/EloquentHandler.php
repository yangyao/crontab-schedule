<?php 

namespace Yangyao\Handler;
use Illuminate\Database\Capsule\Manager as DB;  
class EloquentHandler extends AbstractHandler
{
    private $options;
    private $table;

    public function __construct(array $options = [] , $table = 'crontab')
    {
        $this->options = array_merge([
            'host'     => 'localhost',
            'port'     => 3306,
            'dbname'   => 'crontab',
            'username' => root,
            'password' => '',
        ], $options);
        $this->table = $table;
        $capsule = new DB; 
        $capsule->addConnection($this->options);
        $capsule->bootEloquent();
    }

    public function get($cron_id){
        return DB::table($this->table)->where('id',$cron_id)->where('enabled',1)->get();
    }

    public function all(){
        return DB::table($this->table)->get();
    }

    public function update($cron_id , $timestamp = time()){
        return DB::table($this->table)->where('id',$cron_id)->update(['last'=>$timestamp]);
    }
}
