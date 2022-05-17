<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employer_financial_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('employer_id');
            $table->unsignedFloat('salary_paid');
            $table->unsignedFloat('insurance')->default(0);
            $table->unsignedFloat('tax')->default(0);
            $table->unsignedFloat('bonus')->default(0);
            $table->timestamp('date');
            $table->timestamps();

            $table->index('employer_id');
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
        Schema::dropIfExists('employer_financial_reports');
    }
};
