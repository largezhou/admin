<?php

namespace App\Admin\Console\Commands;

use App\Admin\Models\Config;
use Illuminate\Console\Command;

class CacheConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:cache-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '缓存配置';

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
     * @return int
     */
    public function handle()
    {
        Config::clearConfigCache();
        $this->info('后台配置缓存已清除');

        Config::getDottedConfigFromCache();
        $this->info('已缓存后台配置');

        return 0;
    }
}
