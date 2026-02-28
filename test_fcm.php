<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    print_r(App\Models\FcmToken::with('cliente:id,cliente,razon_social,email')->orderBy('creado_en', 'desc')->paginate(50)->toArray());
} catch (\Exception $e) {
    echo $e->getMessage();
}
