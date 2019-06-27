<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SystemMediaCategoryRequest;
use App\Models\SystemMediaCategory;

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
        $inputs  = $request->validated();
        $systemMediaCategory->update($inputs);
        return $this->created($systemMediaCategory);
    }
}
