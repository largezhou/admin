<?php

namespace App\Admin\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
        if (!$deleted) {
            throw new FileException('文件记录删除失败');
        }

        // 如果数据库中没有其他相同的文件，则删除物理文件
        if (!$this->hasSameFile()) {
            $storage = Storage::disk('uploads');
            // 如果文件存在，并且删除失败，则抛错
            if ($storage->exists($this->path) && !$storage->delete($this->path)) {
                DB::rollBack();
                throw new FileException('物理文件删除失败');
            }
        }

        DB::commit();
        return true;
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
