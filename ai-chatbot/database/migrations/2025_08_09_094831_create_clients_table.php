<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('api_token', 80)->unique();
            $table->string('language')->default('ru');
            $table->enum('plan',['trial','basic','standard','premium'])->default('trial');
            $table->integer('dialog_limit')->default(200);
            $table->integer('dialog_used')->default(0);
            $table->integer('prompts_limit')->default(1);
            $table->integer('prompt_max_length')->default(300);
            $table->integer('rate_limit')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('clients'); }
    
};
