<?php

use App\Admin\Models\VueRouter;
use Illuminate\Database\Seeder;

class VueRoutersTableSeeder extends Seeder
{
    public function run()
    {
        VueRouter::truncate();
        // 暂时弄一个写死的首页，方便前端显示
        factory(VueRouter::class)->create([
            'path' => '/index',
            'title' => '首页',
            'order' => 0,
        ]);
        $vueRouters = factory(VueRouter::class, 9)->make([
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();
        // 三级嵌套菜单
        foreach ($vueRouters as $i => &$router) {
            if ($i < 3) {
                $parentId = 0;
            } else {
                $parentId = $i - 1;
            }
            $router['parent_id'] = $parentId;
        }
        unset($router);

        VueRouter::insert($vueRouters);
    }
}
