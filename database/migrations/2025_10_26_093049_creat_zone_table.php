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

         Schema::create('zone', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->text('description')->nullable();
         
            $table->float('radius');
            $table->foreignId('company_id')->constrained('company')->onDelete('cascade');
        });
        DB::statement('ALTER TABLE zone ADD center_coordinates POINT NOT NULL');
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone');
        //
    }
};
