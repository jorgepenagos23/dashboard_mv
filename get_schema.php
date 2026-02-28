<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['pedidos', 'pedido_producto', 'productos', 'cliente', 'direcciones', 'stock', 'product_prices'];
$output = "";

foreach ($tables as $table) {
    try {
        $result = DB::select("SHOW CREATE TABLE {$table}");
        $row = (array)$result[0];
        $tbl = array_values($row)[1];
        $output .= $tbl . ";\n\n";
    } catch (\Exception $e) {
        $output .= "-- Table {$table} not found or error: " . $e->getMessage() . "\n\n";
    }
}
file_put_contents(__DIR__.'/schema_clean.txt', $output);
