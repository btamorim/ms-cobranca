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
        Schema::create('ticket', function (Blueprint $table) {
            $table->increments->id('ticketId');
            $table->integer('debtId')->foreignId('debtId')->constrained('debit');
            $table->float('amount');
            $table->dateTime('createdAt')->default(now());
            $table->dateTime('expirationDate');
            $table->string('barCode');
            $table->boolean('statusId')->default(0);
            $table->dateTime('paidAt')->nullable(true);
            $table->string('paidBy')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket');
    }
};
