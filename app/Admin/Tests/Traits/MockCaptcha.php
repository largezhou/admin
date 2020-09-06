<?php

namespace App\Admin\Tests\Traits;

use Mockery;

trait MockCaptcha
{
    public function mockCaptcha(bool $result, string $captcha, string $key)
    {
        $this->app->bind('captcha', function ($app) use ($captcha, $key, $result) {
            $mockCaptcha = Mockery::mock(\Mews\Captcha\Captcha::class);

            $mockCaptcha->allows()
                ->check_api($captcha, $key)
                ->andReturn($result);

            return $mockCaptcha;
        });
    }
}
