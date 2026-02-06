<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


            // kada je korisnik ušao u konverzaciju (bitno za istoriju)
            $table->timestamp('joined_at')->nullable();

            $table->unsignedBigInteger(column: 'last_read_message_id')->nullable();
            $table->foreign('last_read_message_id')->references('id')->on('messages')->onDelete('cascade');

            // jedan korisnik ne može duplo biti u istoj konverzaciji
            $table->unique(['conversation_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversation_user');
    }
};
