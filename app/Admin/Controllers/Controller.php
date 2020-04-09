<?php

namespace App\Admin\Controllers;

use App\Admin\Traits\RestfulResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use RestfulResponse;
    /**
     * 上传根目录
     */
    const UPLOAD_FOLDER_PREFIX = 'uploads';

    /**
     * 保存请求中的文件到 storage，并返回各文件相关的信息
     *
     * @param Request $request
     * @param string $folder 存储的文件夹
     *
     * @return array
     *
     * 返回数据示例
     * [
     *     'file' => [
     *         'filename' => 'filename.jpg',
     *         'ext' => 'jpg',
     *         'path' => '/path/to/filename.jpg',
     *         'size' => 10240,
     *         'mime_type' => 'image/jpeg',
     *     ],
     *     'other' => [...],
     * ]
     */
    protected function saveFiles(Request $request, string $folder = null): array
    {
        $files = $request->file();
        $driver = Storage::disk('uploads');

        $folder = static::UPLOAD_FOLDER_PREFIX.($folder ? '/'.trim($folder, '/') : '');

        $files = array_map(function (UploadedFile $file) use ($driver, $folder) {
            $md5 = md5_file($file);
            $ext = $file->getClientOriginalExtension();

            $filename = $md5.($ext ? ".{$ext}" : '');

            $path = $driver->putFileAs($folder, $file, $filename);

            return [
                'filename' => $filename,
                'ext' => $ext,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        }, $files);

        return $files;
    }
}
