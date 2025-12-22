<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Surveys (master table)
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->boolean('require_login')->default(false);
            $table->json('target_groups')->nullable(); // ['mahasiswa_s1', 'dosen', etc.]
            $table->integer('response_count')->default(0);
            $table->timestamps();
        });

        // Survey Sections (e.g., Tangible, Reliability, etc.)
        Schema::create('survey_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Tangible (Bukti Fisik)"
            $table->string('name_en')->nullable(); // English name
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Survey Questions
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('survey_sections')->cascadeOnDelete();
            $table->text('text'); // Question text
            $table->enum('type', ['likert', 'text', 'select', 'rating', 'number'])->default('likert');
            $table->json('options')->nullable(); // For select type: [{"value": 1, "label": "Option 1"}]
            $table->integer('min_value')->nullable(); // For likert/rating scale
            $table->integer('max_value')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Survey Responses (one per respondent per survey)
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('respondent_type')->nullable(); // mahasiswa_s1, mahasiswa_s2, dosen, tendik, tamu, etc.
            $table->string('respondent_name')->nullable();
            $table->string('respondent_email')->nullable();
            $table->string('respondent_faculty')->nullable();
            $table->string('respondent_department')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_complete')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            // Prevent duplicate submissions
            $table->unique(['survey_id', 'ip_address', 'submitted_at']);
        });

        // Survey Answers (one per question per response)
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('survey_responses')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('survey_questions')->cascadeOnDelete();
            $table->text('answer')->nullable(); // Text answer
            $table->integer('score')->nullable(); // Numeric score (1-5 for likert)
            $table->timestamps();
            
            $table->unique(['response_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('survey_sections');
        Schema::dropIfExists('surveys');
    }
};
