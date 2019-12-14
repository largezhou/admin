<?php

namespace App\Admin\Controllers;

use App\Admin\Exceptions\VueRouterException;
use App\Admin\Requests\VueRouterRequest;
use App\Admin\Resources\VueRouterResource;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\VueRouter;
use Illuminate\Http\Request;

class VueRouterController extends Controller
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

    public function edit(Request $request, VueRouter $vueRouter)
    {
        $formData = $this->formData($vueRouter->id);

        $vueRouter->load('roles');
        $vueRouterData = VueRouterResource::make($vueRouter)
            ->onlyRolePermissionIds()
            ->toArray($request);

        return $this->ok(array_merge($formData, [
            'vue_router' => $vueRouterData,
        ]));
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

    public function batchUpdate(Request $request, VueRouter $vueRouter)
    {
        $vueRouter->saveOrder($request->input('_order', []));

        return $this->created();
    }

    /**
     * 返回添加和编辑表单时用到的选项数据
     *
     * @param int $exceptRouterId 要排除的 路由配置 id，编辑表单用到
     *
     * @return array
     */
    protected function formData($exceptRouterId = null)
    {
        $model = app(VueRouter::class);

        if ($exceptRouterId) {
            $vueRouters = $model->treeExcept($exceptRouterId)->toTree();
        } else {
            $vueRouters = $model->toTree();
        }
        $roles = AdminRole::query()
            ->orderByDesc('id')
            ->get();
        $permissions = AdminPermission::query()
            ->orderByDesc('id')
            ->get();

        return [
            'vue_routers' => $vueRouters,
            'roles' => $roles,
            'permissions' => $permissions,
        ];
    }

    public function create()
    {
        return $this->ok($this->formData());
    }

    public function importVueRouters(VueRouterRequest $request, VueRouter $vueRouter)
    {
        $file = $request->file('file');
        try {
            $vueRouters = $vueRouter->replaceFromFile($file);
            return $this->created($vueRouters);
        } catch (VueRouterException $e) {
            return $this->error($e->getMessage());
        }
    }
}
