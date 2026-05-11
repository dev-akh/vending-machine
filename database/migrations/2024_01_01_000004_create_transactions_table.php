<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('total_price', 8, 3);
            $table->decimal('amount_paid', 8, 3);
            $table->decimal('change_given', 8, 3)->default(0);
            $table->enum('status', ['completed', 'pending', 'failed'])->default('completed');
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['product_id']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
