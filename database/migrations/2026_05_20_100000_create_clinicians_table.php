<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('specialty')->nullable();
            $table->string('status')->default('active'); // active | inactive
            $table->unsignedInteger('current_caseload')->default(0);
            $table->unsignedInteger('max_caseload')->default(0);
            $table->decimal('average_sessions_per_week', 6, 2)->default(0);
            $table->decimal('avg_billable_rate', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['practice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinicians');
    }
};
