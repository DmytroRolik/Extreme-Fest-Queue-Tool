<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisCheckConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check redis connection';

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
            $result = Redis::ping();
            if ($result) {
                echo "Connection success\r\n";
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

        echo $result;
    }
}
