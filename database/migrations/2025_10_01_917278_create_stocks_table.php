<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('stocks')) {
            Schema::create('stocks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('city');
                $table->unsignedInteger('stock')->default(0);
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->unique(['product_id', 'city']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
