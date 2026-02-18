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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->text('body')->nullable();

            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('attachment_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reply_to_id')->nullable();

            $table->foreign('reply_to_id')->references('id')->on('messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
