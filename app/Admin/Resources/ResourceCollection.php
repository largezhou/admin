<?php

namespace App\Admin\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResourceCollection extends AnonymousResourceCollection
{
    public static $wrap = null;

    public function withResponse($request, $response)
    {
        $data = $response->getData(true);
        unset($data['links']);
        unset($data['meta']['path']);
        $response->setData($data);
    }
}
