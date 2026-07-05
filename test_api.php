<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = App\Models\User::first();
$token = $user->createToken('test')->plainTextToken;

$req = Illuminate\Http\Request::create('/api/categories', 'POST', [], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'CONTENT_TYPE' => 'application/json',
    'HTTP_AUTHORIZATION' => 'Bearer ' . $token
], json_encode(['name' => 'Kategori Baru Lagi']));

$res = $kernel->handle($req);
echo "Status: " . $res->getStatusCode() . "\n";
echo "Content: " . $res->getContent() . "\n";
