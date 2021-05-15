<?php

namespace Tests\Admin\Feature;

use App\Models\AdminUser;
use Tests\Admin\AdminTestCase;
use Tests\Admin\Traits\MockCaptcha;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use MockCaptcha;

    public function testLogin()
    {
        AdminUser::factory(1)->create([
            'username' => 'admin',
            'password' => bcrypt('000000'),
        ]);

        $url = route('admin.login');

        // 需要验证码
        $this->mockCaptcha(false);
        $this->post($url, [
            'username' => 'admin',
            'password' => '000000',
            'captcha' => 'wrong',
            'key' => 'some key',
        ])->assertStatus(422);

        $this->mockCaptcha(true);
        $this->post($url, [
            'username' => 'admin',
            'password' => '000000',
            'captcha' => '1234',
            'key' => 'some key',
        ])->assertStatus(201);

        // 不需要验证码
        config(['admin.system_basic.admin_login_captcha' => '0']);

        $this->post($url, [
            'username' => 'admin',
            'password' => 'wrong',
        ])->assertStatus(422);

        $this->post($url, [
            'username' => 'admin',
            'password' => '000000',
        ])->assertStatus(201);
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
