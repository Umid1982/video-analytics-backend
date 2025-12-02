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
        Schema::create('analysis_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('video_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report_type')->default('summary')->comment('summary, detailed, heatmap');
            $table->json('analytics_data')->nullable();
            $table->json('heatmap_data')->nullable();
            $table->text('ai_generated_summary')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('session_id');
            $table->index('user_id');
            $table->index('report_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_reports');
    }
};
