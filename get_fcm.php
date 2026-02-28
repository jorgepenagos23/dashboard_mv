<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['fcm_tokens'];

foreach ($tables as $table) {
    try {
        $result = DB::connection('mysql')->select("SHOW CREATE TABLE {$table}");
        $row = (array)$result[0];
        $tbl = array_values($row)[1];
        echo $tbl . ";\n\n";
    } catch (\Exception $e) {
        echo "-- Table {$table} not found or error: " . $e->getMessage() . "\n";
    }
}
