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
        Schema::create('convo_read_by', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('attachment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('message_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            $table->index('conversation_id');
            $table->index('attachment_id');
            $table->index('message_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convo_read_by');
    }
};
