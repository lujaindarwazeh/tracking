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
        Schema::create('companyevent', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('company_id')->constrained('company')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('event')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companyevent');
    }
};
