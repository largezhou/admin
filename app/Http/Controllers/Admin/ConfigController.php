<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ConfigFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigRequest;
use App\Http\Resources\ConfigResource;
use App\Models\Config;
use App\Models\VueRouter;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function vueRouters(VueRouter $vueRouter)
    {
        return $this->ok($vueRouter->treeWithAuth()->toTree());
    }

    public function destroy(Config $config)
    {
        $config->delete();
        return $this->noContent();
    }

    public function edit(Config $config)
    {
        return $this->ok(ConfigResource::make($config));
    }

    public function update(ConfigRequest $request, Config $config)
    {
        $inputs = $request->validated();
        $config->update($inputs);
        return $this->created(ConfigResource::make($config));
    }

    public function index(ConfigFilter $filter)
    {
        $configs = Config::query()
            ->with('category')
            ->filter($filter)
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(
            ConfigResource::collection($configs)
                ->additional([
                    'type_map' => Config::$typeMap,
                ])
        );
    }
}
