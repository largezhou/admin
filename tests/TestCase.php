<?php

namespace Tests;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

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

    /**
     * 获取数据库最新插入的 id
     *
     * @param string|null $table 指定表名，或者整个数据库
     *
     * @return mixed|string
     */
    protected function getLastInsertId(string $table = null)
    {
        if (is_null($table)) {
            return DB::getPdo()->lastInsertId();
        } else {
            return DB::table($table)->orderByDesc('id')->value('id');
        }
    }
}
