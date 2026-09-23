<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('performance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->onDelete('cascade');
            $table->string('period_type')->default('quarter'); // quarter | year
            $table->date('date_period'); // first day of the period
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->decimal('total_expenses', 14, 2)->default(0);
            $table->unsignedInteger('total_clients')->default(0);
            $table->unsignedInteger('total_sessions')->default(0);

            // Optional / future fields — nullable so MVP entry stays light.
            $table->decimal('marketing_spend', 14, 2)->nullable();
            $table->decimal('payroll', 14, 2)->nullable();
            $table->decimal('admin_costs', 14, 2)->nullable();
            $table->json('insurance_mix')->nullable();
            $table->unsignedInteger('cancellations')->nullable();
            $table->unsignedInteger('no_shows')->nullable();

            $table->timestamps();

            $table->unique(['practice_id', 'period_type', 'date_period']);
            $table->index(['practice_id', 'date_period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_logs');
    }
};
