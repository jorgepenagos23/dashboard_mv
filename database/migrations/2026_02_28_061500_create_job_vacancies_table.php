<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sqlite_vacancies';

    public function up(): void
    {
        if (!Schema::connection($this->connection)->hasTable('job_vacancies')) {
            Schema::connection($this->connection)->create('job_vacancies', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->text('requirements');
                $table->string('location');
                $table->boolean('is_active')->default(true);
                $table->string('image_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('job_vacancies');
    }
};
