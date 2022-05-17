<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_financial_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('worker_id');
            $table->unsignedFloat('income');
            $table->unsignedFloat('insurance')->default(0);
            $table->unsignedFloat('tax')->default(0);
            $table->unsignedFloat('fine')->default(0);
            $table->unsignedFloat('bonus')->default(0);
            $table->timestamp('date');
            $table->timestamps();

            $table->index('worker_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('worker_financial_reports');
    }
};
