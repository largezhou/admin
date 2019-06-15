<?php

namespace Tests;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    /**
     * @var
     */
    protected $user;
    /**
     * @var string
     */
    protected $token;
}
