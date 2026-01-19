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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('message_id');
            $table->foreign('message_id')
                  ->references('id')
                  ->on('messages')
                  ->onDelete('cascade'); // Ako se poruka obriše, brišu se i attachment-i
            
            // Tip fajla
            $table->enum('type', ['image', 'video', 'audio']);
            
            // Putanja fajla
            $table->string('path');
            $table->string('thumbnail')->nullable();
            
            // Metadata
            $table->unsignedBigInteger('size')->nullable();       // veličina u bajtovima
            $table->unsignedInteger('width')->nullable();         // za slike
            $table->unsignedInteger('height')->nullable();        // za slike
            $table->unsignedInteger('duration')->nullable();      // za video u sekundama

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
