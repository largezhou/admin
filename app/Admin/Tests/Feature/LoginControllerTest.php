<?php

namespace App\Admin\Tests\Feature;

use App\Admin\Models\AdminUser;
use App\Admin\Utils\Admin;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends AdminTestCase
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
        $res->assertStatus(201);
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
