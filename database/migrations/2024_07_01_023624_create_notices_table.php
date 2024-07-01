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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('sender_type')
                ->default(2)
                ->comment('1: All, 2: Option');
            $table->text('content');
            $table->boolean('is_schedule')->default(0);
            $table->dateTime('published_at')->nullable();
            $table->dateTime('published_end_at')->nullable();
            $table->boolean('is_send')->default(0);
            $table->text('user_ids')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
