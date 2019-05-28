<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminMenuRequest;
use App\Models\Admin\AdminMenu;
use Illuminate\Http\Request;

class AdminMenuController extends AdminBaseController
{
    public function store(AdminMenuRequest $request, AdminMenu $model)
    {
        $inserts = $request->validated();
        $model::create($inserts);
        return $this->created();
    }

    public function update(AdminMenuRequest $request, AdminMenu $menu)
    {
        $inputs = $request->validated();
        $menu->update($inputs);

        return $this->ok();
    }
}
