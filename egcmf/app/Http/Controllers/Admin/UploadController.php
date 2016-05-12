<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UploadNewFolderRequest;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\Controller;
use App\Services\UploadsManager;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    protected $manager;

    public function __construct(UploadsManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Show page of files / subfolders
     */
    public function index(Request $request)
    {
        $folder = $request->get('folder');
        $data = $this->manager->folderInfo($folder);

        return view('admin.upload.index', $data);
    }

    /**
     * 创建新目录
     */
    public function createFolder(UploadNewFolderRequest $request)
    {
        $new_folder = $request->get('new_folder');
        $folder = $request->get('folder').'/'.$new_folder;

        $result = $this->manager->createDirectory($folder);

        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("Folder '$new_folder' created.");
        }

        $error = $result ? : "An error occurred creating directory.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }

    /**
     * 删除文件
     */
    public function deleteFile(Request $request)
    {
        $del_file = $request->get('del_file');
        $path = $request->get('folder').'/'.$del_file;

        $result = $this->manager->deleteFile($path);

        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("File '$del_file' deleted.");
        }

        $error = $result ? : "An error occurred deleting file.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }

    /**
     * 删除目录
     */
    public function deleteFolder(Request $request)
    {
        $del_folder = $request->get('del_folder');
        $folder = $request->get('folder').'/'.$del_folder;

        $result = $this->manager->deleteDirectory($folder);

        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("Folder '$del_folder' deleted.");
        }

        $error = $result ? : "An error occurred deleting directory.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }

    /**
     * 上传文件
     */
    /*
    public function uploadFile(UploadFileRequest $request)
    {
        $file = $_FILES['file'];
        $fileName = $request->get('file_name');
        $fileName = $fileName ?: $file['name'];
        $path = str_finish($request->get('folder'), '/') . $fileName;
        $content = File::get($file['tmp_name']);

        $result = $this->manager->saveFile($path, $content);

        if ($result === true) {
            return redirect()
                ->back()
                ->withSuccess("File '$fileName' uploaded.");
        }

        $error = $result ? : "An error occurred uploading file.";
        return redirect()
            ->back()
            ->withErrors([$error]);
    }
    */
    /**
     * 上传文件
     */
    public function uploadFile(UploadFileRequest $request)
    {
        $file = $request->file('file');
        if ($file->isValid()) {
            $file_name = trim($request->get('file_name'));
            $file_name = $this->genFileName($file, $file_name);

            $path = str_finish($request->get('folder'), '/') . $file_name;
            $content = File::get($file->getRealPath());

            $result = $this->manager->saveFile($path, $content);

            if ($result === true) {
                return redirect()
                    ->back()
                    ->withSuccess("[ $file_name ]上传成功。");
            } else {
                $error = $result ? : "[ $file_name ]上传失败。";
            }
        } else {
            $error = $file->getErrorMessage();
        }

        return redirect()
            ->back()
            ->withErrors([$error]);
    }

    /**
     * 生成上传的文件名
     */
    private function genFileName($file, $new_name)
    {
        if (empty($new_name)) {
            $new_name = str_random(16);
        }

        $ext = $file->getClientOriginalExtension();
        $file_name = preg_replace("/\.{$ext}$/i", '', $new_name);
        if (!empty($ext)) {
            $file_name .= ".{$ext}";
        }

        return $file_name;
    }

}