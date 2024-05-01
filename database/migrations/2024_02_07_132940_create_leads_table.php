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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('business_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('business_id')->references('id')->on('business');
            $table->tinyInteger('ti_status')->default(2);
            $table->string('location')->nullable();
            $table->string('remark')->nullable();
            $table->string('selfie')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
