<?php

use App\Models\TransactionCategory;
use App\Models\User;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date')->default(now());
            $table->bigInteger('amount')->default(0);
            $table->foreignIdFor(TransactionCategory::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->longText('note')->nullable();
            $table->enum('type', ['income', 'expense'])->default('expense');
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
        Schema::dropIfExists('transactions');
    }
};
