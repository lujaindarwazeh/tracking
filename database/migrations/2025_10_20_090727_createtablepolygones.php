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
        Schema::create('polygons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('way_id')->constrained('ways')->onDelete('cascade');
            $table->json('coordinate'); //// stores raw points as JSON
            $table->geometry('geometry'); //stores buffered polygon
        });



        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polygons');
        //
    }
};
