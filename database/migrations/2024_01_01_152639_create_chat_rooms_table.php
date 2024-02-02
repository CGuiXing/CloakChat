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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('name'); // Name of the chat room
            $table->text('tags'); // Tags for the chat room (can be JSON or comma-separated values)
            $table->unsignedBigInteger('owner_id'); // ID of the owner (user ID)
            $table->timestamps(); // Created_at and updated_at timestamps

            // Foreign key constraint for the owner_id referencing the users table (assuming you have a users table)
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};
