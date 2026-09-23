<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('practices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // Add practice_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('practice_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First remove the foreign key from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['practice_id']);
            $table->dropColumn('practice_id');
        });

        // Then drop the practices table
        Schema::dropIfExists('practices');
    }
};
