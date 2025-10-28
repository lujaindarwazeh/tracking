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
        Schema::create('vehicle', function (Blueprint $table) {
            $table->id();
            $table->string('namecar', 100);
            $table->string('serialnumber', 50);
            $table->dateTime('lastupdatetime')->nullable();
            $table->double('longitude')->nullable();
            $table->double('latitude')->nullable();
            $table->double('odometer')->nullable();
            $table->string('drivername', 100)->nullable();
            
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle');
    }
};
