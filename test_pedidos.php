<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $db = \Illuminate\Support\Facades\DB::connection('mysql');
    
    $pedidosCols = \Illuminate\Support\Facades\Schema::connection('mysql')->getColumnListing('pedidos');
    $ppCols = \Illuminate\Support\Facades\Schema::connection('mysql')->getColumnListing('pedido_producto');
    
    echo "Pedidos Cols: " . implode(', ', $pedidosCols) . "\n";
    echo "PedidoProducto Cols: " . implode(', ', $ppCols) . "\n";
    
    $sampleP = $db->table('pedidos')->first();
    print_r($sampleP);
    
    if ($sampleP) {
        $samplePP = $db->table('pedido_producto')->where('pedido_id', $sampleP->id)->first();
        print_r($samplePP);
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
