<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $result = DB::connection('mysql')->select("SHOW CREATE TABLE promociones");
    $row = (array)$result[0];
    $tbl = array_values($row)[1];
    echo $tbl . ";\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
