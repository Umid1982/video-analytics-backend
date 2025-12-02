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
        Schema::create('video_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique()->comment('FastAPI session ID');
            $table->enum('status', ['started','pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('source_type')->default('file')->comment('file, url, stream');
            $table->string('source_path')->nullable();
            $table->integer('total_frames')->default(0);
            $table->integer('total_people')->default(0);
            $table->integer('peak_people_count')->default(0);
            $table->decimal('average_stay_time', 10, 2)->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_sessions');
    }
};
