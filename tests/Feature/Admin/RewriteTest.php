<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RewriteTest extends TestCase
{
    public function testRewrite()
    {
        $base = '/admin';

        $res = $this->get($base);
        $res->assertStatus(200)
            ->assertSee('id="admin-app"');

        $res = $this->get($base.'/any');
        $res->assertStatus(200)
            ->assertSee('id="admin-app"');

        $res = $this->get($base.'404');
        $res->assertStatus(404);
    }
}
