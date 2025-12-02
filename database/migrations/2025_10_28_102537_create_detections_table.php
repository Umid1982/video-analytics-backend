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
        Schema::create('detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('video_sessions')->onDelete('cascade');
            $table->integer('frame_number');
            $table->string('class_name')->default('person');
            $table->decimal('confidence', 5, 4)->default(0);
            $table->decimal('bbox_x', 10, 4);
            $table->decimal('bbox_y', 10, 4);
            $table->decimal('bbox_width', 10, 4);
            $table->decimal('bbox_height', 10, 4);
            $table->integer('track_id')->nullable()->comment('DeepSORT track ID');
            $table->timestamp('detected_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('session_id');
            $table->index('frame_number');
            $table->index('track_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
