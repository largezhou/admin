<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminMenuRequest;
use App\Http\Resources\AdminMenuResource;
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

    public function edit(AdminMenu $menu)
    {
        return AdminMenuResource::make($menu);
    }

    public function index()
    {
        return $this->ok(AdminMenu::buildNestedArray());
    }

    public function destroy(AdminMenu $menu)
    {
        $menu->delete();
        return $this->noContent();
    }
}
