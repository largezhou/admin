<?php

namespace App\Admin\Controllers;

use App\Admin\Filters\SystemMediaFilter;
use App\Admin\Requests\SystemMediaCategoryRequest;
use App\Admin\Requests\SystemMediaRequest;
use App\Admin\Resources\SystemMediaResource;
use App\Admin\Models\SystemMediaCategory;
use Illuminate\Http\Request;

class SystemMediaCategoryController extends Controller
{
    public function store(SystemMediaCategoryRequest $request)
    {
        $inputs = $request->validated();
        $cate = SystemMediaCategory::create($inputs);
        return $this->created($cate);
    }

    public function update(
        SystemMediaCategoryRequest $request,
        SystemMediaCategory $systemMediaCategory
    ) {
        $inputs = $request->validated();
        $systemMediaCategory->update($inputs);
        return $this->created($systemMediaCategory);
    }

    public function edit(SystemMediaCategory $systemMediaCategory)
    {
        return $this->ok($systemMediaCategory);
    }

    public function destroy(SystemMediaCategory $systemMediaCategory)
    {
        $systemMediaCategory->delete();
        return $this->noContent();
    }

    public function index(Request $request, SystemMediaCategory $model)
    {
        return $this->ok($model->treeExcept((int) $request->input('except'))->toTree());
    }

    /**
     * 上传文件到指定分类下
     *
     * @param SystemMediaRequest $request
     * @param SystemMediaCategory $systemMediaCategory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSystemMedia(SystemMediaRequest $request, SystemMediaCategory $systemMediaCategory)
    {
        $files = $this->saveFiles($request);
        $media = $systemMediaCategory->media()->create($files['file']);
        return $this->created(SystemMediaResource::make($media));
    }

    /**
     * 获取分类下的所有文件
     *
     * @param SystemMediaCategory $systemMediaCategory
     * @param SystemMediaFilter $filter
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function systemMediaIndex(SystemMediaCategory $systemMediaCategory, SystemMediaFilter $filter)
    {
        $media = $systemMediaCategory->media()
            ->filter($filter)
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(SystemMediaResource::collection($media));
    }
}
