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

            // Kojoj konverzaciji pripada poruka
            $table->unsignedBigInteger(column: 'conversation_id')->nullable();
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');

            $table->unsignedBigInteger(column: 'sender_id')->nullable();
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');


            // Tekst poruke
            $table->text('message');

            // Opcionalno: status poruke (sent, delivered, read) možeš dodati kasnije
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');

            $table->timestamps(); // created_at & updated_at

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
