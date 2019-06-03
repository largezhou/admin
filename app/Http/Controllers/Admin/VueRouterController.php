<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\VueRouterRequest;
use App\Http\Resources\VueRouterResource;
use App\Models\Admin\VueRouter;
use Illuminate\Http\Request;

class VueRouterController extends AdminBaseController
{
    public function store(VueRouterRequest $request, VueRouter $model)
    {
        $inputs = $request->validated();
        $model::create($inputs);
        return $this->created();
    }

    public function update(VueRouterRequest $request, VueRouter $vueRouter)
    {
        $inputs = $request->validated();
        $vueRouter->update($inputs);

        return $this->ok();
    }

    public function edit(VueRouter $vueRouter)
    {
        return VueRouterResource::make($vueRouter);
    }

    public function index()
    {
        return $this->ok(VueRouter::buildNestedArray());
    }

    public function destroy(VueRouter $vueRouter)
    {
        $vueRouter->delete();
        return $this->noContent();
    }
}
