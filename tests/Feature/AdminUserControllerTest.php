<?php

namespace Tests\Feature;

use App\Http\Resources\AdminUserResource;
use Tests\AdminTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUserControllerTest extends AdminTestCase
{
    use RefreshDatabase;

    public function testInfo()
    {
        $url = route('admin.user');

        $this->get($url)->assertStatus(401);

        $this->login();

        $user = $this->user;
        $res = $this->get($url);
        $res->assertJson(AdminUserResource::make($user)->toArray(app('request')));
    }
}
