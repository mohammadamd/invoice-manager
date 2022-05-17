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
        Schema::create('employer_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 21);
            $table->unsignedInteger('employer_id');
            $table->unsignedFloat('creditor')->default(0);
            $table->unsignedFloat('debtor')->default(0);
            $table->unsignedFloat('current_balance');
            $table->unsignedInteger('transaction_type');
            $table->timestamps();

            $table->index('reference');
            $table->index('transaction_type');
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
        Schema::dropIfExists('employer_transactions');
    }
};
