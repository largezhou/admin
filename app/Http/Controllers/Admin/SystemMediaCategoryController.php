<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SystemMediaCategoryRequest;
use App\Models\SystemMediaCategory;
use Illuminate\Http\Request;

class SystemMediaCategoryController extends AdminBaseController
{
    public function store(SystemMediaCategoryRequest $request)
    {
        $inputs = $request->validated();
        $cate = SystemMediaCategory::create($inputs);
        return $this->created($cate);
    }

    public function update(SystemMediaCategoryRequest $request, SystemMediaCategory $systemMediaCategory)
    {
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
}
