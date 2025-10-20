<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::middleware(['api.log', 'jwt.auth'])->any('{module}/{path?}', function (Request $request, $module, $path = '') {
    $moduleUrlMapping = config('gateway.modules');

    $lowerModule = strtolower($module);

    if (!isset($moduleUrlMapping[$lowerModule])) {
        return response()->json(['message' => 'Module not found or not configured in gateway'], 404);
    }

    $targetUrl = $moduleUrlMapping[$lowerModule] . '/' . $path;

    $pendingRequest = Http::withHeaders($request->headers->all());

    if ($request->isJson()) {
        $pendingRequest->withBody($request->getContent(), 'application/json');
    } elseif ($request->isMultipart()) {
        $multipart = [];
        foreach ($request->all() as $name => $value) {
            $multipart[] = ['name' => $name, 'contents' => $value];
        }
        foreach ($request->file() as $name => $file) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }
        $pendingRequest->asMultipart()->withOptions(['multipart' => $multipart]);
    } else {
        $pendingRequest->withBody($request->getContent(), $request->header('Content-Type'));
    }

    $response = $pendingRequest->send($request->method(), $targetUrl, [
        'query' => $request->query(),
        'cookies' => $request->cookies->all(),
    ]);

    return response($response->body(), $response->status())
        ->withHeaders($response->headers());

})->where('path', '.*');
