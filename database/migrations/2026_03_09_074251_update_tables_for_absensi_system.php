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

    
    Schema::table('users', function (Blueprint $table) {
        $table->string('nip')->nullable();
    });

    Schema::table('school_classes', function (Blueprint $table) {
        $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
    });

    Schema::table('attendances', function (Blueprint $table) {
        $table->foreignId('class_id')->nullable()->constrained('school_classes');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
