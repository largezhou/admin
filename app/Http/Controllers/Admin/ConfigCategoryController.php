<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ConfigCategoryFilter;
use App\Http\Requests\ConfigCategoryRequest;
use App\Http\Resources\ConfigCategoryResource;
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
        return $this->created(ConfigCategory::create($request->validated()));
    }

    public function update(ConfigCategoryRequest $request, ConfigCategory $configCategory)
    {
        return $this->created($configCategory->update($request->validated()));
    }

    public function destroy(ConfigCategory $configCategory)
    {
        $configCategory->delete();
        return $this->noContent();
    }
}
