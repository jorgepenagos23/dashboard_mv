<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tokens = \App\Models\FcmToken::with(['cliente:id,cliente,razon_social,email', 'cliente.direcciones:CTECLI_CODIGO_K,CTEPFR_CODIGO_K'])
        ->limit(5)->get();
    
    foreach($tokens as $t) {
        $pfr = $t->cliente && $t->cliente->direcciones->isNotEmpty() ? $t->cliente->direcciones->first()->CTEPFR_CODIGO_K : 'N/A';
        echo "- Token: " . $t->id . " PFR: " . $pfr . "\n";
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
