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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id')->nullable()->unique();
            $table->string('uid')->unique();
            $table->foreignId('investor_id')->constrained()->onDelete('cascade');
            $table->foreignId('fund_id')->constrained()->onDelete('cascade');
            $table->decimal('capital_amount', 15, 2);
            $table->date('start_date');
            $table->string('status');
            $table->timestamp('api_created_at')->nullable();
            $table->timestamp('api_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
