<?php

define('LARAVEL_START', microtime(true));

if (file_exists(__DIR__.'/../rutvans/backend/storage/framework/maintenance.php')) {
    require __DIR__.'/../rutvans/backend/storage/framework/maintenance.php';
}

require __DIR__.'/../rutvans/backend/vendor/autoload.php';

$app = require_once __DIR__.'/../rutvans/backend/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);