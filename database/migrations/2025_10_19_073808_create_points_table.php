<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('way_id')->constrained('ways')->onDelete('cascade');
            });

// Add spatial column using raw SQL
           DB::statement('ALTER TABLE points ADD location POINT NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
