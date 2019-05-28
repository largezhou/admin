<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminMenuRequest;
use App\Models\Admin\AdminMenu;
use Illuminate\Http\Request;

class AdminMenuController extends AdminBaseController
{
    public function store(AdminMenuRequest $request, AdminMenu $model)
    {
        $inputs = $request->validated();
        $model::create($inputs);
        return $this->created();
    }

    public function update(AdminMenuRequest $request, AdminMenu $menu)
    {
        $inputs = $request->validated();
        $menu->update($inputs);

        return $this->ok();
    }
}
