<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Product name
            $table->decimal('price', 10, 2)->default(0); // Price
            $table->string('package')->nullable(); // Package information
            $table->string('link')->nullable(); // Link to the product
            $table->string('supplier_name')->nullable(); // Supplier name
            $table->string('supplier_country')->nullable(); // Supplier country
            $table->string('supplier_type')->nullable(); // Supplier type
            $table->string('cas_no')->nullable(); // CAS number
            $table->string('grade')->nullable(); // Grade
            $table->string('content')->nullable(); // Content percentage
            $table->string('brand')->nullable(); // Brand name
            $table->string('packaging')->nullable(); // Packaging details
            $table->string('price_valid')->nullable(); // Price validity
            $table->string('image_url')->nullable(); // Image URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
