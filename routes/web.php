<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api-docs', function () {
    return view('swagger');
});

Route::get('/api-docs/json', function () {
    $spec = json_decode(file_get_contents(storage_path('api-docs/openapi.json')));
    $spec->servers[0]->url = url('/api');
    return response()->json($spec);
});
