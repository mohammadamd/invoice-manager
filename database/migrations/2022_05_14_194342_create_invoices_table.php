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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('worker_id');
            $table->unsignedInteger('employer_id');
            $table->unsignedFloat('net_income');
            $table->unsignedFloat('gross_income');
            $table->unsignedFloat('fine')->default(0);
            $table->unsignedFloat('commission')->default(0);
            $table->unsignedFloat('insurance')->default(0);
            $table->unsignedFloat('bonus')->default(0);
            $table->unsignedFloat('tax')->default(0);
            $table->unsignedFloat('employer_payable');
            $table->unsignedFloat('temper_payable')->default(0);
            $table->timestamps();

            $table->index('worker_id');
            $table->index('employer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice');
    }
};
