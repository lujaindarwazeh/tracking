<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('device_id')->references('id')->on('device')->onDelete('cascade');
            $table->string('providername', 100);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider');
    }
};
