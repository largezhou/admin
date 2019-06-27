<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SystemMediaCategoryRequest;
use App\Models\SystemMediaCategory;

class SystemMediaCategoryController extends AdminBaseController
{
    public function store(SystemMediaCategoryRequest $request, SystemMediaCategory $model)
    {
        $inputs = $request->validated();
        $cate = $model->create($inputs);
        return $this->created($cate);
    }
}
