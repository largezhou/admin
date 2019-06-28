<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VueRouterRequest;
use App\Http\Resources\VueRouterResource;
use App\Models\VueRouter;
use Illuminate\Http\Request;

class VueRouterController extends AdminBaseController
{
    public function store(VueRouterRequest $request, VueRouter $vueRouter)
    {
        $inputs = $request->validated();
        $vueRouter = $vueRouter->create($inputs);
        if (!empty($q = $request->post('roles', []))) {
            $vueRouter->roles()->attach($q);
        }
        return $this->created(VueRouterResource::make($vueRouter));
    }

    public function update(VueRouterRequest $request, VueRouter $vueRouter)
    {
        $inputs = $request->validated();
        $vueRouter->update($inputs);
        if (isset($inputs['roles'])) {
            $vueRouter->roles()->sync($inputs['roles']);
        }

        return $this->created(VueRouterResource::make($vueRouter));
    }

    public function edit(VueRouter $vueRouter)
    {
        $vueRouter->load('roles');
        return $this->ok(VueRouterResource::make($vueRouter)->onlyRolePermissionIds());
    }

    public function index(Request $request, VueRouter $vueRouter)
    {
        return $this->ok($vueRouter->treeExcept((int) $request->input('except'))->toTree());
    }

    public function destroy(VueRouter $vueRouter)
    {
        $vueRouter->delete();
        return $this->noContent();
    }
}
