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
        Schema::create('extra_repayment_schedule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_detail_id');


            $table->unsignedInteger('month_number');
            $table->decimal('starting_balance', 10, 2);
            $table->decimal('monthly_payment', 10, 2);
            $table->decimal('principal', 10, 2);
            $table->decimal('interest', 10, 2);
            $table->decimal('extra_repayment', 10, 2)->nullable();
            $table->decimal('ending_balance', 10, 2)->nullable();
            $table->unsignedInteger('remaining_loan_term');
            $table->timestamps();

            $table->foreign('loan_detail_id')
            ->references('id')
                ->on('loan_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_repayment_schedule');
    }
};
