<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Course Categories (harus dibuat dulu)
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Courses (Kelas)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('duration_hours')->nullable();
            $table->integer('max_participants')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('schedule_time')->nullable();
            $table->string('schedule_days')->nullable(); // JSON: ["monday","wednesday"]
            $table->string('location')->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('meeting_link')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('requires_approval')->default(false);
            $table->boolean('has_certificate')->default(true);
            $table->integer('passing_score')->default(70);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Course Modules (Modul/Bab)
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Course Materials (Materi)
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('title');
            $table->text('content')->nullable();
            $table->enum('type', ['text', 'video', 'document', 'link', 'quiz'])->default('text');
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('external_link')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Course Enrollments (Pendaftaran Peserta)
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'dropped'])->default('pending');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->integer('progress_percent')->default(0);
            $table->integer('final_score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['course_id', 'member_id']);
        });

        // Material Progress (Progress Materi per Peserta)
        Schema::create('course_material_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('course_materials')->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamps();
            
            $table->unique(['enrollment_id', 'material_id']);
        });

        // Course Quizzes
        Schema::create('course_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('course_materials')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit_minutes')->nullable();
            $table->integer('passing_score')->default(70);
            $table->integer('max_attempts')->default(3);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('show_correct_answers')->default(false);
            $table->timestamps();
        });

        // Quiz Questions
        Schema::create('course_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('course_quizzes')->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'essay'])->default('multiple_choice');
            $table->json('options')->nullable(); // For multiple choice
            $table->text('correct_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->integer('points')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Quiz Attempts
        Schema::create('course_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('course_quizzes')->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->json('answers')->nullable();
            $table->integer('score')->nullable();
            $table->boolean('is_passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        // Course Certificates
        Schema::create('course_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->timestamp('issued_at');
            $table->foreignId('issued_by')->constrained('users');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // Course Announcements
        Schema::create('course_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_announcements');
        Schema::dropIfExists('course_certificates');
        Schema::dropIfExists('course_quiz_attempts');
        Schema::dropIfExists('course_quiz_questions');
        Schema::dropIfExists('course_quizzes');
        Schema::dropIfExists('course_material_progress');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
    }
};
