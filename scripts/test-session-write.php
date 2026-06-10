<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$before = DB::table('sessions')->count();

$request = Request::create('/', 'GET');
$response = $kernel->handle($request);
$kernel->terminate($request, $response);

$after = DB::table('sessions')->count();
$latest = DB::table('sessions')->orderByDesc('last_activity')->first();

$cookies = $response->headers->all('set-cookie');
$hasSessionCookie = false;
foreach ($cookies as $cookie) {
    if (strpos($cookie, 'myapp2_session=') !== false) {
        $hasSessionCookie = true;
    }
}

echo "DB: ".config('database.connections.'.config('database.default').'.database')."\n";
echo "driver: ".config('session.driver')."\n";
echo "sessions before: {$before}\n";
echo "sessions after:  {$after}\n";
echo "session cookie set: ".($hasSessionCookie ? 'yes' : 'no')."\n";
if ($latest) {
    echo "latest id prefix: ".substr($latest->id, 0, 20)."...\n";
    echo "latest last_activity: ".date('Y-m-d H:i:s', $latest->last_activity)."\n";
    echo "payload bytes: ".strlen(base64_decode($latest->payload))."\n";
}
