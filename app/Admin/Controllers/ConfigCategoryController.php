<?php

namespace App\Admin\Controllers;

use App\Admin\Filters\ConfigCategoryFilter;
use App\Admin\Requests\ConfigCategoryRequest;
use App\Admin\Resources\ConfigCategoryResource;
use App\Admin\Models\ConfigCategory;
use Illuminate\Http\Request;

class ConfigCategoryController extends Controller
{
    public function index(Request $request, ConfigCategoryFilter $filter)
    {
        $categories = ConfigCategory::query()
            ->filter($filter)
            ->orderByDesc('id');
        $categories = $request->input('all')
            ? $categories->get()
            : $categories->paginate();

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
}
