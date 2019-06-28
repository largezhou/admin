<?php

namespace App\Models;

use App\Traits\ModelTree;

class SystemMediaCategory extends Model
{
    use ModelTree;
    protected $fillable = ['parent_id', 'name'];
    protected $casts = [
        'parent_id' => 'integer',
    ];

    public function media()
    {
        return $this->hasMany(SystemMedia::class, 'cate_id');
    }

    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = $value ?: 0;
    }

    protected function orderColumn()
    {
        return 'id';
    }
}
