<?php

namespace App\Admin\Console\Commands;

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use App\Admin\Models\Config;
use App\Admin\Models\ConfigCategory;
use App\Admin\Models\VueRouter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdminInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
    admin:init
    {--F|force : 强制执行，不询问}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化基础路由配置，超级管理员角色和权限';

    public static $initConfirmTip = '初始化操作，会清空路由、管理员、角色和权限表，以及相关关联表数据。是否确认？';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('force') || $this->confirm(static::$initConfirmTip)) {
            $this->initDemo();
            $this->info('初始化完成，管理员为：admin，密码为：000000');
            return 1;
        } else {
            return 0;
        }
    }

    protected function initDemo()
    {
        DB::unprepared(file_get_contents(__DIR__.'/data/admin_init.sql'));
    }
}
