<?php

namespace Tests\Admin\Unit;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\AdminTestCase;

class HasPermissionsTest extends AdminTestCase
{
    use RefreshDatabase;

    public function testMethods()
    {
        /** @var AdminUser $user */
        $user = AdminUser::factory()->create();

        $role1 = AdminRole::factory()->create(['slug' => 'role_1']);
        $perm1 = AdminPermission::factory()->create(['slug' => 'perm_1']);
        $role1->permissions()->attach($perm1->id);
        $user->roles()->attach($role1->id);

        $role2 = AdminRole::factory()->create(['slug' => 'role_2']);

        $perm2 = AdminPermission::factory()->create(['slug' => 'perm_2']);
        $user->permissions()->attach($perm2->id);

        $perm3 = AdminPermission::factory()->create(['slug' => 'perm_3']);

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
        $admin = AdminRole::factory()->create(['slug' => 'administrator']);
        $user->roles()->attach($admin->id);
        $user->setRelation('roles', $user->roles()->get());
        $this->assertTrue($user->can('perm_3'));
        $this->assertTrue($user->visible(['role_2']));
    }
}
