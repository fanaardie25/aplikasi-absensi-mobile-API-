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
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('class_id');
            $table->index('schedule_class_id');
            $table->index('created_at');
            $table->index(['student_id', 'schedule_class_id', 'created_at'], 'check_attendance_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
        $table->dropIndex('check_attendance_idx');
        $table->dropIndex(['student_id']);
        $table->dropIndex(['class_id']);
        $table->dropIndex(['schedule_class_id']);
        $table->dropIndex(['created_at']);
        });
    }
};
