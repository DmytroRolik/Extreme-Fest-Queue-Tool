<?php

namespace App\Console\Commands;

use App\Classes\Queue\QueueRedis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisReloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload data in redis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            QueueRedis::reload();
            echo "Redis successfully reloaded\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
