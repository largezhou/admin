<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin()
    {
        factory(AdminUser::class, 1)->create([
            'username' => 'admin',
            'password' => bcrypt('000000'),
        ]);

        $url = route('admin.login');

        $this->post($url, [
            'username' => 'admin',
            'password' => 'wrong',
        ])->assertStatus(422);

        $res = $this->post($url, [
            'username' => 'admin',
            'password' => '000000',
        ]);
        $auth = auth('admin_api');
        $res->assertStatus(201)
            ->assertJson([
                'token' => $auth->getToken()->get(),
                'token_type' => 'bearer',
                'expired_in' => $auth->factory()->getTTL() * 60,
            ]);
    }

    public function testLogout()
    {
        $url = route('admin.logout');

        $this->post($url)->assertStatus(401);

        $this->login();
        $this->post($url)->assertStatus(204);

        $this->post($url)->assertStatus(401);
    }
}
