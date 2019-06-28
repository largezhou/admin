<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemMedia extends Model
{
    protected $fillable = [
        'filename', 'ext', 'category_id', 'path', 'size', 'mime_type',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'size' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(SystemMediaCategory::class, 'category_id');
    }

    public function delete()
    {
        DB::beginTransaction();

        $deleted = parent::delete();

        // 记录删除成功，则继续删除文件
        $fileDeleted = false;
        if ($deleted) {
            $storage = Storage::disk('uploads');
            // 文件不存在，或者删除成功
            if (!$storage->exists($this->path) || $storage->delete($this->path)) {
                $fileDeleted = true;
            }
        }

        if ($deleted && $fileDeleted) {
            DB::commit();
            return true;
        } else {
            DB::rollBack();
            return false;
        }
    }
}
