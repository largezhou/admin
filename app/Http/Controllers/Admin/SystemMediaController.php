<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SystemMediaRequest;
use App\Http\Resources\SystemMediaResource;
use App\Models\SystemMedia;
use Illuminate\Http\Request;

class SystemMediaController extends AdminBaseController
{
    public function destroy(SystemMedia $systemMedia)
    {
        $systemMedia->delete();
        return $this->noContent();
    }

    public function edit(SystemMedia $systemMedia)
    {
        return $this->ok(SystemMediaResource::make($systemMedia));
    }

    public function update(SystemMediaRequest $request, SystemMedia $systemMedia)
    {
        $inputs = $request->validated();
        $systemMedia->update($inputs);
        return $this->created($systemMedia);
    }
}
