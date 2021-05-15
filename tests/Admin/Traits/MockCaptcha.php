<?php

namespace Tests\Admin\Traits;

use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Mockery\MockInterface;

trait MockCaptcha
{
    use InteractsWithContainer;

    public function mockCaptcha(bool $result)
    {
        $this->mock('captcha', function (MockInterface $mock) use ($result) {
            $mock->shouldReceive('check_api')->andReturn($result);
        });
    }
}
