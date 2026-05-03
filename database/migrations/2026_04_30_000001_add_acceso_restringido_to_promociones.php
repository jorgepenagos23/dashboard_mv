<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql')->table('promociones', function (Blueprint $table) {
            $table->tinyInteger('acceso_restringido')->default(0)->after('foto_banner');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->table('promociones', function (Blueprint $table) {
            $table->dropColumn('acceso_restringido');
        });
    }
};
