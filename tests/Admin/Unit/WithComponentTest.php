<?php

namespace Tests\Admin\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WithComponentTest extends TestCase
{
    public function testWithComponent()
    {
        Route::get('/test-xxx', function () {
        })->middleware('with-component');

        $this->get('/test-xxx')->assertHeaderMissing('Vue-Component');

        Route::get('/test1-xxx', DummyController::class.'@xxx')->middleware('with-component');
        $this->get('/test1-xxx')->assertHeader('Vue-Component', 'dummy/xxx');

        Route::get('/test2-xxx', DummyController::class.'@yyy')->middleware('with-component');
        $this->get('/test2-xxx')->assertHeader('Vue-Component', 'dummy-single/yyy');
    }
}

class DummyController extends Controller
{
    public function xxx()
    {
    }

    public function yyy()
    {
        return $this->ok()->withComponent('dummy-single/yyy');
    }
}
