<?php

namespace App\Admin\Models;

class ConfigCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function configs()
    {
        return $this->hasMany(Config::class, 'category_id');
    }

    public function delete()
    {
        $res = parent::delete();
        $this->configs->each->delete();
        return $res;
    }
}
