<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ConfigCategoryFilter;
use App\Http\Requests\ConfigCategoryRequest;
use App\Http\Requests\ConfigRequest;
use App\Http\Resources\ConfigCategoryResource;
use App\Http\Resources\ConfigResource;
use App\Models\ConfigCategory;
use Illuminate\Http\Request;

class ConfigCategoryController extends AdminBaseController
{
    public function index(ConfigCategoryFilter $filter)
    {
        $categories = ConfigCategory::query()
            ->filter($filter)
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(ConfigCategoryResource::collection($categories));
    }

    public function store(ConfigCategoryRequest $request)
    {
        $inputs = $request->validated();
        $cate = ConfigCategory::create($inputs);
        return $this->created(ConfigCategoryResource::make($cate));
    }

    public function update(ConfigCategoryRequest $request, ConfigCategory $configCategory)
    {
        $inputs = $request->validated();
        $configCategory->update($inputs);
        return $this->created(ConfigCategoryResource::make($configCategory));
    }

    public function destroy(ConfigCategory $configCategory)
    {
        $configCategory->delete();
        return $this->noContent();
    }

    public function storeConfig(ConfigRequest $request, ConfigCategory $configCategory)
    {
        $inputs = $request->validated();
        $config = $configCategory->configs()->create($inputs);

        return $this->created(ConfigResource::make($config));
    }
}
