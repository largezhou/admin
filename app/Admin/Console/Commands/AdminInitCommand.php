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
            $this->createVueRouters();
            $this->createUserRolePermission();
            $this->createDefaultConfigs();
            $this->info('初始化完成，管理员为：admin，密码为：000000');
            return 1;
        } else {
            return 0;
        }
    }

    protected function createVueRouters()
    {
        $inserts = [
            [1, 0, '首页', 'index', 0, null, 1, 0],

            [2, 105, '路由配置', null, 1, null, 1, 0],
            [3, 2, '所有路由', 'vue-routers', 2, null, 1, 1],
            [4, 2, '添加路由', 'vue-routers/create', 3, null, 1, 0],
            [5, 2, '编辑路由', 'vue-routers/:id(\\d+)/edit', 4, null, 0, 0],

            [6, 0, '管理员管理', null, 5, null, 1, 0],
            [7, 6, '管理员列表', 'admin-users', 6, null, 1, 0],
            [8, 6, '添加管理员', 'admin-users/create', 7, null, 1, 0],
            [9, 6, '编辑管理员', 'admin-users/:id(\\d+)/edit', 8, null, 0, 0],

            [10, 0, '角色管理', null, 9, null, 1, 0],
            [11, 10, '角色列表', 'admin-roles', 10, null, 1, 0],
            [12, 10, '添加角色', 'admin-roles/create', 11, null, 1, 0],
            [13, 10, '编辑角色', 'admin-roles/:id(\\d+)/edit', 12, null, 0, 0],

            [14, 0, '权限管理', null, 13, null, 1, 0],
            [15, 14, '权限列表', 'admin-permissions', 14, null, 1, 0],
            [16, 14, '添加权限', 'admin-permissions/create', 15, null, 1, 0],
            [17, 14, '编辑权限', 'admin-permissions/:id(\\d+)/edit', 16, null, 0, 0],

            [18, 0, '文件管理', 'system-media', 17, null, 1, 0],

            [19, 0, '配置管理', null, 18, null, 1, 0],
            [20, 19, '配置分类', 'config-categories', 19, null, 1, 0],

            [99, 0, '权限测试', '/permission-test', 99, null, 1, 0],

            [21, 19, '所有配置', 'configs', 20, null, 1, 0],
            [22, 19, '添加配置', 'configs/create', 21, null, 1, 0],
            [23, 19, '编辑配置', 'configs/:id(\\d+)/edit', 22, null, 0, 0],

            [24, 0, '系统设置', '/configs/system_basic', 23, null, 1, 0],

            [100, 0, '菜单匹配测试', null, 100, null, 1, 0],
            [101, 100, '链接', 'https://www.baidu.com', 101, null, 1, 0],
            [102, 100, '编辑路由 3', '/vue-routers/3/edit', 102, null, 1, 0],
            [103, 100, '带 query a=1', '/admin-users?a=1', 103, null, 1, 0],
            [104, 100, '带 query b=1&a=1', '/admin-users?b=1&a=1', 104, null, 1, 0],

            [105, 0, '嵌套1', null, 1, null, 1, 0],
        ];

        $inserts = $this->combineInserts(
            ['id', 'parent_id', 'title', 'path', 'order', 'icon', 'menu', 'cache'],
            $inserts,
            [
                'created_at' => now(),
                'updated_at' => now(),
                'permission' => null,
            ]
        );

        VueRouter::truncate();
        VueRouter::insert($inserts);
    }

    protected function createUserRolePermission()
    {
        AdminUser::truncate();
        AdminRole::truncate();
        AdminPermission::truncate();

        collect(['admin_role_permission', 'admin_user_permission', 'admin_user_role', 'vue_router_role'])
            ->each(function ($table) {
                DB::table($table)->truncate();
            });

        $user = AdminUser::create([
            'name' => '管理员',
            'username' => 'admin',
            'password' => bcrypt('000000'),
        ]);

        $user->roles()->create([
            'name' => '超级管理员',
            'slug' => 'administrator',
        ]);

        AdminRole::first()
            ->permissions()
            ->create([
                'name' => '所有权限',
                'slug' => 'pass-all',
                'http_path' => '*',
            ]);
    }

    protected function createDefaultConfigs()
    {
        $categories = [
            [1, '系统设置', 'system_basic'],
        ];
        $categories = $this->combineInserts(
            ['id', 'name', 'slug'],
            $categories,
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $configs = [
            [1, Config::TYPE_INPUT, '系统名称', 'app_name', null, null, json_encode('后台'), 'required|string|max:20'],
            [1, Config::TYPE_FILE, '系统 LOGO', 'app_logo', null, '{"max":1,"ext":"jpg,png,jpeg"}', null, 'nullable|string'],
            [1, Config::TYPE_FILE, '登录背景图', 'login_background', null, '{"max":1,"ext":"jpg,png,jpeg"}', null, 'nullable|string'],
            [1, Config::TYPE_OTHER, '首页路由', 'home_route', null, null, json_encode('1'), 'required|exists:vue_routers,id'],
            [1, Config::TYPE_INPUT, 'CDN 域名', 'cdn_domain', null, null, json_encode('/'), 'required|string'],
        ];
        $configs = $this->combineInserts(
            ['category_id', 'type', 'name', 'slug', 'desc', 'options', 'value', 'validation_rules'],
            $configs,
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        ConfigCategory::truncate();
        ConfigCategory::insert($categories);

        Config::truncate();
        Config::insert($configs);
    }

    /**
     * 组合字段和对应的值
     *
     * @param array $fields 字段
     * @param array $inserts 值，不带字段的
     * @param array $extra 每列都相同的数据，带字段
     *
     * @return array
     */
    protected function combineInserts(array $fields, array $inserts, array $extra): array
    {
        return array_map(function ($i) use ($fields, $extra) {
            $i = array_combine($fields, $i);
            return array_merge($i, $extra);
        }, $inserts);
    }
}
