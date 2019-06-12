<?php

namespace App\Models\Admin;

use App\Models\Model;
use Illuminate\Support\Str;

class AdminPermission extends Model
{
    protected $fillable = ['name', 'slug', 'http_method', 'http_path'];
    public static $httpMethods = [
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD',
    ];

    public function setHttpMethodAttribute($method)
    {
        if (is_array($method)) {
            $this->attributes['http_method'] = implode(',', $method) ?: null;
        } else {
            $this->attributes['http_method'] = $method;
        }
    }

    public function getHttpMethodAttribute($method)
    {
        return array_filter(explode(',', $method));
    }

    public function setHttpPathAttribute($httpPath)
    {
        if (is_array($httpPath)) {
            $this->attributes['http_path'] = implode("\n", $httpPath) ?: null;
        } else {
            $this->attributes['http_path'] = $httpPath;
        }
    }

    public function getHttpPathAttribute($httpPath)
    {
        return array_filter(explode("\n", $httpPath));
    }
}
