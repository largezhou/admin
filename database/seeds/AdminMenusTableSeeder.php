<?php

use App\Models\Admin\AdminMenu;
use Illuminate\Database\Seeder;

class AdminMenusTableSeeder extends Seeder
{
    public function run()
    {
        AdminMenu::truncate();
        // 暂时弄一个写死的首页，方便前端显示
        factory(AdminMenu::class)->create([
            'uri' => '/index',
            'title' => '首页',
            'order' => 0,
        ]);
        $menus = factory(AdminMenu::class, 9)->make([
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();
        // 三级嵌套菜单
        foreach ($menus as $i => &$menu) {
            if ($i < 3) {
                $parentId = 0;
            } else {
                $parentId = $i - 1;
            }
            $menu['parent_id'] = $parentId;
        }
        unset($menu);

        AdminMenu::insert($menus);
    }
}
