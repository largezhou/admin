<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUserRoleAndPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_role', function (Blueprint $table) {
            $table->integer('user_id')->index();
            $table->integer('role_id')->index();
        });
        Schema::create('admin_user_permission', function (Blueprint $table) {
            $table->integer('user_id')->index();
            $table->integer('permission_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user_role');
        Schema::dropIfExists('admin_user_permission');
    }
}
