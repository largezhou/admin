<?php

namespace App\Admin\Tests\Unit;

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Admin\Tests\AdminTestCase;

class HasPermissionsTest extends AdminTestCase
{
    use RefreshDatabase;

    public function testMethods()
    {
        /** @var AdminUser $user */
        $user = factory(AdminUser::class)->create();

        $role1 = factory(AdminRole::class)->create(['slug' => 'role_1']);
        $perm1 = factory(AdminPermission::class)->create(['slug' => 'perm_1']);
        $role1->permissions()->attach($perm1->id);
        $user->roles()->attach($role1->id);

        $role2 = factory(AdminRole::class)->create(['slug' => 'role_2']);

        $perm2 = factory(AdminPermission::class)->create(['slug' => 'perm_2']);
        $user->permissions()->attach($perm2->id);

        $perm3 = factory(AdminPermission::class)->create(['slug' => 'perm_3']);

        // allPermissions
        $allPerms = $user->allPermissions()->pluck('slug')->all();
        $this->assertTrue(count(array_intersect($allPerms, ['perm_1', 'perm_2'])) == 2);

        // can
        $this->assertTrue($user->can('perm_1'));
        $this->assertTrue($user->can('perm_2'));
        $this->assertFalse($user->can('perm_3'));

        // isRole
        $this->assertTrue($user->isRole('role_1'));
        $this->assertFalse($user->isRole('role_2'));

        // inRoles
        $this->assertTrue($user->inRoles(['role_1', 'role_2']));
        $this->assertFalse($user->inRoles(['role_2']));

        // visible
        $this->assertTrue($user->visible([$role1, $role2]));
        $this->assertFalse($user->visible([$role2]));

        // administrator
        $admin = factory(AdminRole::class)->create(['slug' => 'administrator']);
        $user->roles()->attach($admin->id);
        $user->setRelation('roles', $user->roles()->get());
        $this->assertTrue($user->can('perm_3'));
        $this->assertTrue($user->visible(['role_2']));
    }
}
