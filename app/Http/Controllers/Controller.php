<?php

namespace App\Http\Controllers;

use App\Traits\RestfulResponse;
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
     * 上传目录，默认为空，即 public/uploads 下
     *
     * @var string
     */
    protected $uploadFolder = '';

    /**
     * 保存文件到本地，并把请求中的数据的文件换成保存后的本地路径
     *
     * @param Request $request
     * @param string $folder 上传目录
     *
     * @return array
     */
    protected function handleUploadFile(Request $request, string $folder = '')
    {
        $files = $request->file();
        $driver = Storage::disk('uploads');
        $folder = trim($this->uploadFolder, '/').($folder ? ('/'.trim($folder, '/')) : '');

        $files = array_map(function (UploadedFile $file) use ($driver, $folder) {
            $md5 = md5_file($file);
            $ext = $file->getClientOriginalExtension();

            $filename = $md5.($ext ? ".{$ext}" : '');

            $path = $driver->putFileAs($folder, $file, $filename);

            return $driver->url($path);
        }, $files);

        if (method_exists($request, 'validated')) {
            $data = array_merge($request->validated(), $files);
        } else {
            $data = array_merge($request->all(), $files);
        }

        return $data;
    }
}
