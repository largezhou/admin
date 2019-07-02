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
    protected $perPage = 30;

    public function category()
    {
        return $this->belongsTo(SystemMediaCategory::class, 'category_id');
    }

    public function delete()
    {
        DB::beginTransaction();

        $deleted = parent::delete();

        // 记录删除成功，且数据库中没有相同的文件记录，则要删除对应的文件
        $needDeletedFile = $deleted && !$this->hasSameFile();

        $fileDeleted = false;
        if ($needDeletedFile) {
            $storage = Storage::disk('uploads');
            // 文件不存在，或者删除成功
            if (!$storage->exists($this->path) || $storage->delete($this->path)) {
                $fileDeleted = true;
            }
        }

        if (
            ($needDeletedFile && $fileDeleted) || // 需要删除物理文件，且删除成功，则提交事务
            (!$needDeletedFile && $deleted) // 或不需要物理删除，且当前记录删除成功
        ) {
            DB::commit();
            return true;
        } else { // 删除失败
            DB::rollBack();
            return false;
        }
    }

    /**
     * 数据库中是否还有相同文件的记录
     *
     * @return bool
     */
    protected function hasSameFile()
    {
        return !!static::query()
            ->where('filename', $this->filename)
            ->where('id', '<>', $this->id)
            ->first();
    }
}
