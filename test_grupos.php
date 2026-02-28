<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $results = \Illuminate\Support\Facades\DB::connection('mysql')
        ->table('direcciones')
        ->select('CTEPFR_CODIGO_K', 'CTECLI_CODIGO_K', 'CTECLI_RAZONSOCIAL')
        ->whereNotNull('CTEPFR_CODIGO_K')
        ->groupBy('CTEPFR_CODIGO_K', 'CTECLI_CODIGO_K', 'CTECLI_RAZONSOCIAL')
        ->limit(10)
        ->get();
        
    print_r($results->toArray());
} catch (\Exception $e) {
    echo $e->getMessage();
}
