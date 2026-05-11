<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 3);
            $table->integer('quantity_available')->unsigned();
            $table->timestamps();
            
            $table->index(['name']);
            $table->index(['price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
