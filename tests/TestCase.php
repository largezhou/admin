<?php

namespace Tests;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    /**
     * @var AdminUser
     */
    protected $user;
    /**
     * @var string
     */
    protected $token;

    protected function login(AdminUser $user = null)
    {
        $user = $user ?: factory(AdminUser::class)->create(['username' => 'admin']);
        $this->actingAs($user, 'admin_api');

        $auth = auth('admin_api');
        $this->user = $user;
        $this->token = $auth->tokenById($user->id);
        $auth->setToken($this->token);
    }
}
