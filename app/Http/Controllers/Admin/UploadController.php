<?php

namespace App\Http\Controllers\Admin;

use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class UploadController
{
    public function process(Request $request, string $id, string $namespace, string $name = 'photos', string $type = 'multiple')
    {
        ini_set('memory_limit', '-1');

        $fileFromRequest = $request->all()[$name] ?? [];

        if ($type == 'single' || !is_array($fileFromRequest)) {
            $fileFromRequest = [$fileFromRequest];
        }

        $additional = $request->all()['additional'] ?? [];
        if (Str::isJson($additional)) {
            $additional = json_decode($additional, true);
        }
        $uploadService = new UploadService();

        $uploadService->processUploadingFiles($fileFromRequest, $namespace, $id, $additional);

        return response()->noContent();
    }

    public function load(Request $request, string $namespace, string $id)
    {
        $file = (new UploadService())->loadFile($namespace, $id);

        return Response::make(file_get_contents($file->file_path), 200)->withHeaders([
            'Access-Control-Expose-Headers' => 'Content-Disposition, Content-Length, X-Content-Transfer-Id',
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline; filename="' . $file->filename . '"',
            'Content-Length' => filesize($file->file_path),
            'X-Content-Transfer-Id' => $id,
        ]);
    }

    public function revert(Request $request, string $namespace)
    {
        $uploadService = new UploadService();
        $uploadService->removeFile($namespace, (int)$request->getContent());

        return response()->noContent();
    }
}
