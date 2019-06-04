<?php

namespace Tests\Admin;

use App\Models\Admin\AdminUser;

class TestCase extends \Tests\TestCase
{
    protected $routePrefix = 'admin';

    protected function login(AdminUser $user = null)
    {
        $user = $user ?: factory(AdminUser::class)->create(['username' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $auth = auth('admin-api');
        $this->user = $user;
        $this->token = $auth->tokenById($user->id);
        $auth->setToken($this->token);
    }
}
