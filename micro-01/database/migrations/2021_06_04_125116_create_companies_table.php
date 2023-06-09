<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('companies', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->uuid();
            $table->string('name')->unique();
            $table->string('url')->unique();
            $table->string('whatsapp')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('facebook')->unique()->nullable();
            $table->string('instagram')->unique()->nullable();
            $table->string('youtube')->unique()->nullable();
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
}
